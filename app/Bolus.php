<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bolus extends Model
{
    public function getMaximumDoseStringAttribute()
    {
        if($this->maximum_dose == 0)
            return '-';

        return $this->maximum_dose . ' ' . $this->unit;
    }

    public function getDose($weight)
    {
        $result = 0;
        $dose = $weight * $this->dosage;
        if($this->maximum_dose > 0 && $dose > $this->maximum_dose)
            $result = $this->maximum_dose;

        else if($this->minimum_dose > 0 && $dose < $this->minimum_dose)
            $result = $this->minimum_dose;

        else
            $result = $dose;

        return $result;
    }

    public function getDoseString($weight)
    {
        return $this->getDose($weight) . ' ' . $this->unit;
    }

    public function getVolume($weight)
    {
        $dose = $this->getDose($weight);

        if($this->unit == 'ml')
            return 3 * $weight;

        if($this->commercial_concentration == 0)
            return -1;

        $volume = $dose / $this->commercial_concentration;

        return round($volume, 2);
    }

    public function getVolumeString($weight)
    {
        $volume = $this->getVolume($weight);
        if($volume == -1)
            return '-';

        return $volume . ' mL';
    }
}
