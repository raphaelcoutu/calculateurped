<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfusionConcentration extends Model
{
    use HasFactory;

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
