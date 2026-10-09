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

    /** @return array<string, mixed> */
    public function preparationAttributes(): array
    {
        return $this->only(['concentration', 'concentration_unit', 'instructions', 'total_volume', 'min_weight', 'max_weight', 'position']);
    }

    protected function casts(): array
    {
        return ['min_weight' => 'float', 'max_weight' => 'float', 'position' => 'integer',
            'concentration' => 'float', 'total_volume' => 'float'];
    }

    public function scopeWeight($query, $category)
    {
        return $query->where('weight_category', $category);
    }
}
