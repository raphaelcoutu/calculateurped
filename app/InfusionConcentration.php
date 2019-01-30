<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfusionConcentration extends Model
{
    protected $guarded = [];

    private $isDebitMinLimited = false;
    private $isDebitMaxLimited = false;

    public function drug()
    {
        return $this->belongsTo(InfusionDrug::class, 'infusion_drug_id');
    }

    public function scopeWeight($query, $category)
    {
        return $query->where('weight_category', $category);
    }

    public function getIsDebitMinLimitedAttribute()
    {
        return $this->isDebitMinLimited;
    }

    public function getIsDebitMaxLimitedAttribute()
    {
        return $this->isDebitMaxLimited;
    }

    public function getDebitMinimal($weight)
    {
        // Exemple : 30 mg/kg/h
        $drugDebit = $this->convertToDoseBaseUnit($this->drug->debit_min, $this->drug->debit_dose_unit);

        // Exemple : 30 mL/h
        $debitMlHour = $this->getDebit($weight, $drugDebit);

        if($this->drug->debit_min_limit > 0) {
            $debitDoseBaseUnitHour = $debitMlHour *
                $this->convertToDoseBaseUnit($this->concentration, $this->concentration_unit);
            $limitDoseBaseUnitHour = $this->convertToDoseBaseUnit($this->drug->debit_min_limit, $this->drug->debit_limit_unit);

            if($debitDoseBaseUnitHour > $limitDoseBaseUnitHour) {
                $this->isDebitMinLimited = true;
                return $this->getFixedDebit($limitDoseBaseUnitHour);
            }
        }

        return $debitMlHour;

    }

    public function getDebitMaximal($weight)
    {
        // Pour l'insuline, on met une valeur nulle
        if($this->drug->debit_max === 0.0) return 0;

        $drugDebit = $this->convertToDoseBaseUnit($this->drug->debit_max, $this->drug->debit_dose_unit);

        $debitMlHour = $this->getDebit($weight, $drugDebit);

        if($this->drug->debit_max_limit > 0) {
            $debitDoseBaseUnitHour = $debitMlHour *
                $this->convertToDoseBaseUnit($this->concentration, $this->concentration_unit);
            $limitDoseBaseUnitHour = $this->convertToDoseBaseUnit($this->drug->debit_max_limit, $this->drug->debit_limit_unit);

            if($debitDoseBaseUnitHour > $limitDoseBaseUnitHour) {
                $this->isDebitMaxLimited = true;
                return $this->getFixedDebit($limitDoseBaseUnitHour);
            }
        }

        return $debitMlHour;
    }

    private function getDebit($weight, $drugDebit)
    {
        $concentration = $this->convertToDoseBaseUnit($this->concentration, $this->concentration_unit);
        $minuteToHour = $this->minToHourFactor($this->drug->debit_time_unit);

        return round($weight * $drugDebit / $concentration * $minuteToHour, 1);
    }

    private function getFixedDebit($doseBaseUnitHour)
    {
        $concentration = $this->convertToDoseBaseUnit($this->concentration, $this->concentration_unit);
        return round($doseBaseUnitHour/ $concentration, 1);
    }

    /**
     * Retourne la dose en unités standardisées (mg, unité) pour faciliter le calcul
     *
     * @param $dose
     * @param $unit
     * @return float
     */
    private function convertToDoseBaseUnit($dose, $unit)
    {
        $baseUnits = ['mg', 'unité'];
        $microUnits = ['mcg', 'mU'];
        if(in_array($unit, $baseUnits)) {
            return $dose;
        } else if(in_array($unit, $microUnits)) {
            return $dose / 1000;
        }
    }

    private function minToHourFactor($timeUnit)
    {
        return $timeUnit === 'min' ? 60 : 1;
    }
}
