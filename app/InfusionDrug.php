<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfusionDrug extends Model
{
    protected $guarded = [];

    public function concentrations()
    {
        return $this->hasMany(InfusionConcentration::class);
    }
}
