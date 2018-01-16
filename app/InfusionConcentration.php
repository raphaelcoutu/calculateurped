<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfusionConcentration extends Model
{
    public function drug()
    {
        return $this->belongsTo(InfusionDrug::class, 'infusion_drug_id');
    }

    public function getDebit($weight)
    {
        $debit = ($this->drug->debit_dose_unit == 'mcg') ? $this->drug->debit_min/1000 : $this->drug->debit_min;
        $concentration = ($this->concentration_unit == 'mcg') ? $this->concentration/1000 : $this->concentration;
        $minuteToHour = ($this->drug->debit_time_unit == 'min') ? 60 : 1;

        return round($weight * $debit / $concentration * $minuteToHour, 2);
    }
}
