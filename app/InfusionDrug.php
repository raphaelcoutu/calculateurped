<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfusionDrug extends Model
{
    public function getDoseStringAttribute()
    {
        return "{$this->debit_min}-{$this->debit_max} {$this->debit_dose_unit}/kg/{$this->debit_time_unit}";
    }

    public function getInitialDebitStringAttribute()
    {
        return "{$this->debit_min} {$this->debit_dose_unit}/kg/{$this->debit_time_unit}";
    }
}
