<?php

namespace App\Models;

use Database\Factories\InfusionDrugFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property Carbon|null $published_at
 * @property Carbon|null $superseded_at
 * @property Carbon|null $deleted_at
 */
class InfusionDrug extends Model
{
    /** @use HasFactory<InfusionDrugFactory> */
    use HasFactory, SoftDeletes;

    protected $attributes = ['dose_per_kg' => true];

    protected $fillable = [
        'organization_id', 'recipe_id', 'version', 'status', 'created_by', 'published_by',
        'published_at', 'superseded_at', 'supersedes_id', 'name', 'brand_name',
        'concentration', 'debit_min', 'debit_max', 'debit_dose_unit', 'debit_time_unit',
        'debit_min_limit', 'debit_max_limit', 'debit_limit_unit', 'dosage_precision', 'dose_per_kg', 'type', 'order',
    ];

    protected function casts(): array
    {
        return [
            'dose_per_kg' => 'boolean',
            'organization_id' => 'integer',
            'version' => 'integer',
            'created_by' => 'integer',
            'published_by' => 'integer',
            'published_at' => 'datetime',
            'superseded_at' => 'datetime',
            'debit_min' => 'float', 'debit_max' => 'float',
            'debit_min_limit' => 'float', 'debit_max_limit' => 'float',
            'dosage_precision' => 'integer', 'type' => 'integer', 'order' => 'integer',
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

    /** @return BelongsTo<InfusionDrug, $this> */
    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(InfusionDrug::class, 'supersedes_id');
    }

    /** @return HasMany<InfusionActivity, $this> */
    public function activities(): HasMany
    {
        return $this->hasMany(InfusionActivity::class, 'infusion_drug_id')->latest();
    }

    /** @param Builder<InfusionDrug> $query */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNull('superseded_at')
            ->whereNull('deleted_at');
    }

    /** @return HasMany<InfusionConcentration, $this> */
    public function concentrations(): HasMany
    {
        return $this->hasMany(InfusionConcentration::class)->orderBy('position')->orderBy('id');
    }

    /** @return array<string, mixed> */
    public function recipeAttributes(): array
    {
        return $this->only([
            'name', 'brand_name', 'concentration', 'debit_min', 'debit_max', 'debit_dose_unit',
            'debit_time_unit', 'debit_min_limit', 'debit_max_limit', 'debit_limit_unit',
            'dosage_precision', 'dose_per_kg', 'type', 'order',
        ]);
    }

    /** @return list<string> */
    public static function doseUnits(): array
    {
        $units = [];
        foreach (['mg', 'mcg', 'unité', 'mU'] as $doseUnit) {
            foreach (['/kg', ''] as $weightUnit) {
                foreach (['h', 'min'] as $timeUnit) {
                    $units[] = $doseUnit.$weightUnit.'/'.$timeUnit;
                }
            }
        }

        return $units;
    }

    public function doseUnit(): string
    {
        return $this->debit_dose_unit.($this->dose_per_kg ? '/kg' : '').'/'.$this->debit_time_unit;
    }

    /** @return Collection<int, InfusionConcentration> */
    public static function preparationsForWeight(float $weight): Collection
    {
        $drugs = self::published()->with(['concentrations' => function ($query) use ($weight): void {
            $query->where('min_weight', '<=', $weight)
                ->where(fn ($query) => $query->whereNull('max_weight')->orWhere('max_weight', '>', $weight));
        }])->orderBy('order')->orderBy('id')->get();

        $preparations = collect();
        foreach ($drugs as $drug) {
            $preparation = $drug->concentrations->first();
            if ($preparation !== null) {
                $preparation->setRelation('drug', $drug);
                $preparations->push($preparation);
            }
        }

        return $preparations;
    }

    /** @return list<array{min: float, max: float}> */
    public function coverageGaps(): array
    {
        $gaps = [];
        $coveredUntil = 0.0;
        foreach ($this->concentrations->sortBy('min_weight') as $preparation) {
            $minimum = min(100.0, $preparation->min_weight);
            if ($minimum > $coveredUntil) {
                $gaps[] = ['min' => $coveredUntil, 'max' => $minimum];
            }
            $coveredUntil = max($coveredUntil, min(100.0, $preparation->max_weight ?? 100.0));
        }
        if ($coveredUntil < 100) {
            $gaps[] = ['min' => $coveredUntil, 'max' => 100.0];
        }

        return $gaps;
    }

    /** @return array<string, mixed> */
    public function recipeSnapshot(): array
    {
        return [...$this->recipeAttributes(), 'dose_unit' => $this->doseUnit(), 'preparations' => $this->concentrations->map(
            fn (InfusionConcentration $preparation): array => $preparation->preparationAttributes()
        )->all()];
    }
}
