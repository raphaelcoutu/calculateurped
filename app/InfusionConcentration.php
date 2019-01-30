<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfusionConcentration extends Model
{
    protected $guarded = [];

    public function drug()
    {
        return $this->belongsTo(InfusionDrug::class, 'infusion_drug_id');
    }

    public function scopeWeight($query, $category)
    {
        return $query->where('weight_category', $category);
    }
}
