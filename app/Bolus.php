<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bolus extends Model
{
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
        return round($this->getDose($weight), $this->precision) . ' ' . $this->unit;
    }

    public function getVolume($weight)
    {
        $dose = $this->getDose($weight);

        if($this->unit == 'mL')
            return 3 * $weight;

        if($this->commercial_concentration == 0)
            return -1;

        $volume = $dose / $this->commercial_concentration;

        return $volume;
    }

    public function getVolumeString($weight)
    {
        $volume = $this->getVolume($weight);
        if($volume == -1)
            return '-';

        return round($volume, $this->precision) . ' mL';
    }
}
