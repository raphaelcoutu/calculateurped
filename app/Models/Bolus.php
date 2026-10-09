<?php

namespace App\Models;

use Database\Factories\BolusFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $published_at
 * @property Carbon|null $superseded_at
 * @property Carbon|null $deleted_at
 */
class Bolus extends Model
{
    /** @use HasFactory<BolusFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'recipe_id', 'version', 'status', 'created_by', 'published_by',
        'published_at', 'superseded_at', 'supersedes_id', 'copied_from_id', 'name',
        'brand_name', 'unit', 'commercial_concentration', 'dosage', 'minimum_dose',
        'maximum_dose', 'dose_precision', 'volume_precision', 'type', 'min_weight',
        'max_weight', 'instructions', 'asterisk',
    ];

    protected function casts(): array
    {
        return [
            'organization_id' => 'integer',
            'version' => 'integer',
            'created_by' => 'integer',
            'published_by' => 'integer',
            'published_at' => 'datetime',
            'superseded_at' => 'datetime',
            'asterisk' => 'boolean',
            'commercial_concentration' => 'float',
            'dosage' => 'float',
            'minimum_dose' => 'float',
            'maximum_dose' => 'float',
            'min_weight' => 'float',
            'max_weight' => 'float',
            'dose_precision' => 'integer',
            'volume_precision' => 'integer',
            'type' => 'integer',
        ];
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /** @return BelongsTo<Bolus, $this> */
    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(Bolus::class, 'supersedes_id');
    }

    /** @return BelongsTo<Bolus, $this> */
    public function copiedFrom(): BelongsTo
    {
        return $this->belongsTo(Bolus::class, 'copied_from_id');
    }

    /** @return HasMany<BolusActivity, $this> */
    public function activities(): HasMany
    {
        return $this->hasMany(BolusActivity::class)->latest();
    }

    /** @param Builder<Bolus> $query */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNull('superseded_at')
            ->whereNull('deleted_at');
    }

    /** @param Builder<Bolus> $query */
    public function scopeWeight(Builder $query, float|int|string|null $weight): Builder
    {
        return $query->published()
            ->where('min_weight', '<=', $weight)
            ->where('max_weight', '>', $weight);
    }

    /** @return array<string, mixed> */
    public function recipeAttributes(): array
    {
        return $this->only([
            'name', 'brand_name', 'unit', 'commercial_concentration', 'dosage',
            'minimum_dose', 'maximum_dose', 'dose_precision', 'volume_precision',
            'type', 'min_weight', 'max_weight', 'instructions', 'asterisk',
        ]);
    }
}
