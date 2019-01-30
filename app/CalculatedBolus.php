<?php

namespace App;

use App\Concerns\HasAttributes;
use ArrayAccess;

class CalculatedBolus implements ArrayAccess
{
    use HasAttributes;

    private $weight;

    public function __construct(Bolus $bolus, $weight)
    {
        $this->bolus = $bolus;
        $this->weight = $weight;

        $this->setValues();
    }

    public function setValues()
    {
        $this->setRoundedVolume();
        $this->setRoundedDose();
        $this->setRoundedVolumeString();
        $this->setRoundedDoseString();
    }

    private function setDose()
    {
        $result = 0;
        $dose = $this->weight * $this->bolus->dosage;

        if ($this->bolus->maximum_dose > 0 && $dose > $this->bolus->maximum_dose) {
            $result = $this->bolus->maximum_dose;
        } else if ($this->bolus->minimum_dose > 0 && $dose < $this->bolus->minimum_dose) {
            $result = $this->bolus->minimum_dose;
        } else {
            $result = $dose;
        }

        return $this->dose = $result;
    }

    private function setVolume()
    {
        $dose = $this->setDose();

        // Exception pour le NaCl 3%
        if ($this->bolus->unit == 'mL') {
            return $this->volume = $dose;
        }

        // Exception pour les joules
        else if ($this->bolus->commercial_concentration === 0.0) {
            return $this->volume = -1;
        }

        else {
            $volume = $dose / $this->bolus->commercial_concentration;
            return $this->volume = $volume;
        }
    }

    private function setRoundedVolume()
    {
        $volume = $this->setVolume();

        return $this->roundedVolume = round($volume, $this->bolus->volume_precision);
    }

    private function setRoundedDose()
    {
        $volume = $this->setRoundedVolume();

        // Si c'est les joules ou le NaCl 3%
        if($this->bolus->commercial_concentration === 0.0) {
            return $this->roundedDose = round($this->dose, $this->bolus->dose_precision);
        } else {
            return $this->roundedDose = round($volume * $this->bolus->commercial_concentration, $this->bolus->dose_precision);
        }
    }

    public function setRoundedDoseString()
    {
        return $this->roundedDoseString = "{$this->roundedDose} {$this->bolus->unit}";
    }

    public function setRoundedVolumeString()
    {
        if($this->roundedVolume === -1) {
            return $this->roundedVolumeString = '-';
        } else {
            return $this->roundedVolumeString = "{$this->roundedVolume} mL";
        }
    }
}