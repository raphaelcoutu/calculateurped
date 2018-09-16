<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfusionConcentration extends Model
{
    public function drug()
    {
        return $this->belongsTo(InfusionDrug::class, 'infusion_drug_id');
    }

    public function getDebitMinimal($weight)
    {
        $drugDebit = ($this->drug->debit_dose_unit == 'mcg') ? $this->drug->debit_min/1000 : $this->drug->debit_min;
        return $this->getDebit($weight, $drugDebit);
    }

    public function getDebitMaximal($weight)
    {
        // Pour l'insuline, on met une valeur nulle
        if($this->drug->debit_max === 0.0) return 0;

        $drugDebit = ($this->drug->debit_dose_unit == 'mcg') ? $this->drug->debit_max/1000 : $this->drug->debit_max;
        return $this->getDebit($weight, $drugDebit);
    }

    private function getDebit($weight, $drugDebit)
    {
        $concentration = ($this->concentration_unit == 'mcg') ? $this->concentration/1000 : $this->concentration;
        $minuteToHour = ($this->drug->debit_time_unit == 'min') ? 60 : 1;

        return round($weight * $drugDebit / $concentration * $minuteToHour, 2);
    }
}
