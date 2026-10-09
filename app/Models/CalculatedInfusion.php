<?php

namespace App\Models;

use App\Concerns\HasAttributes;
use LogicException;

/**
 * @property InfusionConcentration $recipe
 * @property InfusionDrug $drug
 * @property float $minimalRate
 * @property float $maximalRate
 * @property bool|null $isRateMinLimited
 * @property bool|null $isRateMaxLimited
 * @property float $minimalDosage
 * @property float $maximalDosage
 * @property string $dosageString
 * @property string $rateString
 */
class CalculatedInfusion
{
    use HasAttributes;

    private $weight;

    public function __construct(InfusionConcentration $recipe, $weight)
    {
        $drug = $recipe->drug;

        if ($drug === null) {
            throw new LogicException('An infusion concentration must reference an existing drug.');
        }

        $this->recipe = $recipe;
        $this->drug = $drug;
        $this->weight = $weight;

        $this->setValues();
    }

    private function setValues()
    {
        $this->calculateMinimalRate();
        $this->calculateMaximalRate();
        $this->setDosages();
        $this->setDosageString();
        $this->setRateString();
    }

    private function calculateMinimalRate()
    {
        // Exemple : 30 mcg/kg/min * 1kg -> 30 mL/h
        $mlHourRate = $this->getRate($this->drug->debit_min);

        $this->minimalRate = $mlHourRate;

        if ($this->drug->debit_min_limit > 0) {
            // Exemple : 200 mcg/h
            $baseUnitPerHour = $this->getBaseUnitPerHour($mlHourRate);
            $baseUnitLimitPerHour = $this->convertDoseToBaseUnit($this->drug->debit_min_limit, $this->drug->debit_limit_unit);

            if ($baseUnitPerHour > $baseUnitLimitPerHour) {
                $this->isRateMinLimited = true;
                $this->minimalRate = $this->getFixedRate($baseUnitLimitPerHour);
            }
        }
    }

    private function calculateMaximalRate()
    {
        // Exemple : 30 mg/kg/h * 1kg -> 30 mL/h
        $mlHourRate = $this->getRate($this->drug->debit_max);

        $this->maximalRate = $mlHourRate;

        if ($this->drug->debit_max_limit > 0) {
            $baseUnitPerHour = $this->getBaseUnitPerHour($mlHourRate);
            $baseUnitLimitPerHour = $this->convertDoseToBaseUnit($this->drug->debit_max_limit, $this->drug->debit_limit_unit);

            if ($baseUnitPerHour > $baseUnitLimitPerHour) {
                $this->isRateMaxLimited = true;
                $this->maximalRate = $this->getFixedRate($baseUnitLimitPerHour);
            }
        }
    }

    private function getBaseUnitPerHour($mlHourRate)
    {
        $concentration = $this->recipe->concentration;
        $concentrationUnit = $this->recipe->concentration_unit;
        $standardConcentration = $this->convertDoseToBaseUnit($concentration, $concentrationUnit);

        return $mlHourRate * $standardConcentration;
    }

    private function getFixedRate($doseBaseUnitHour)
    {
        $concentration = $this->convertDoseToBaseUnit($this->recipe->concentration, $this->recipe->concentration_unit);
        $rate = $doseBaseUnitHour / $concentration;
        $precision = ($rate < 2) ? 2 : 1;

        return $this->floorp($rate, $precision);
    }

    private function getRate(float $dosageRate): float
    {
        $standardDosage = $this->convertDoseToBaseUnit($dosageRate, $this->drug->debit_dose_unit);
        $concentration = $this->convertDoseToBaseUnit($this->recipe->concentration, $this->recipe->concentration_unit);
        $minuteToHourFactor = $this->minToHourFactor($this->drug->debit_time_unit);
        $weightFactor = $this->drug->dose_per_kg ? $this->weight : 1;
        $rate = $weightFactor * $standardDosage / $concentration * $minuteToHourFactor;

        return $this->floorp($rate, 2);
    }

    /**
     * Fonction pour arrondir à l'inférieur (bug math avec PHP Floor)
     */
    public function floorp($val, $precision)
    {
        // Éviter une valeur négative (ex: maxRate = 0)
        if ($val == 0) {
            return 0;
        }

        $factor = pow(10, $precision);
        $scaledValue = $val * $factor;
        $floatingPointTolerance = PHP_FLOAT_EPSILON * max(1, abs($scaledValue)) * 4;

        return floor($scaledValue + $floatingPointTolerance) / $factor;
    }

    /**
     * Retourne la dose en unités standardisées (mg, unité) pour faciliter le calcul
     *
     * @return float
     */
    private function convertDoseToBaseUnit($dose, $unit)
    {
        $baseUnits = ['mg', 'unité'];
        $microUnits = ['mcg', 'mU'];
        if (in_array($unit, $microUnits)) {
            $dose /= 1000;
        }

        return $dose;
    }

    private function convertDosageToDrugUnit(float $mlHourRate): float
    {
        $baseUnits = ['mg', 'unité'];
        $microUnits = ['mcg', 'mU'];

        $conc = $this->recipe->concentration;
        $concUnit = $this->recipe->concentration_unit;

        $dosage = $mlHourRate * $conc;

        // Dose
        $dosage *= $this->convertUnitFactor($concUnit, $this->drug->debit_dose_unit);

        if ($this->drug->dose_per_kg) {
            $dosage /= $this->weight;
        }

        // Time
        $dosage /= $this->minToHourFactor($this->drug->debit_time_unit);

        return round($dosage, $this->drug->dosage_precision);
    }

    private function setDosageString(): void
    {
        $dosage = $this->minimalDosage;

        if ($this->maximalDosage > 0 && $this->minimalDosage !== $this->maximalDosage) {
            $dosage .= " - {$this->maximalDosage}";
        }

        $dosage .= ' '.$this->drug->doseUnit();

        $this->dosageString = $dosage;
    }

    private function setRateString()
    {
        $rate = $this->minimalRate;

        if ($this->maximalRate > 0 && $this->minimalRate !== $this->maximalRate) {
            $rate .= " - {$this->maximalRate}";
        }

        $rate .= ' mL/h';

        $this->rateString = $rate;
    }

    private function convertUnitFactor($fromUnit, $toUnit)
    {
        // Le nombre correspond à : j'ai besoin de X unit pour former l'unité standard.
        $units = [
            'mg' => 1,
            'unité' => 1,
            'mcg' => 1000,
            'mU' => 1000,
        ];

        return $units[$toUnit] / $units[$fromUnit];
    }

    private function minToHourFactor($timeUnit)
    {
        return $timeUnit === 'min' ? 60 : 1;
    }

    private function setDosages()
    {
        // Pour éviter d'avoir des minDosage > maxDosage, on recalcule les 2
        if ($this->isRateMinLimited || $this->isRateMaxLimited) {
            $this->minimalDosage = $this->convertDosageToDrugUnit($this->minimalRate);
            $this->maximalDosage = $this->convertDosageToDrugUnit($this->maximalRate);
        } else {
            $this->minimalDosage = round($this->drug->debit_min, $this->drug->dosage_precision);
            $this->maximalDosage = round($this->drug->debit_max, $this->drug->dosage_precision);
        }
    }
}
