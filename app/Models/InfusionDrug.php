<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfusionDrug extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function concentrations()
    {
        return $this->hasMany(InfusionConcentration::class);
    }
}
