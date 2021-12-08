<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bolus extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeWeight($query, $weight)
    {
        return $query->where('min_weight', '<=', $weight)
            ->where('max_weight', '>', $weight);
    }
}
