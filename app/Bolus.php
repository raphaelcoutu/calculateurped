<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bolus extends Model
{
    protected $guarded = [];

    public function scopeWeight($query, $weight)
    {
        return $query->where('min_weight', '<=', $weight)
            ->where('max_weight', '>', $weight);
    }
}
