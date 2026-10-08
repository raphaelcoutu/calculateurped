<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfusionConcentration extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<InfusionDrug, $this>
     */
    public function drug(): BelongsTo
    {
        return $this->belongsTo(InfusionDrug::class, 'infusion_drug_id');
    }

    public function scopeWeight($query, $category)
    {
        return $query->where('weight_category', $category);
    }
}
