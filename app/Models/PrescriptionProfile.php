<?php

namespace App\Models;

use Database\Factories\PrescriptionProfileFactory;
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
class PrescriptionProfile extends Model
{
    /** @use HasFactory<PrescriptionProfileFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['organization_id', 'profile_id', 'version', 'name', 'status', 'created_by', 'published_by', 'published_at', 'superseded_at', 'supersedes_id'];

    protected function casts(): array
    {
        return ['organization_id' => 'integer', 'version' => 'integer', 'created_by' => 'integer', 'published_by' => 'integer', 'published_at' => 'datetime', 'superseded_at' => 'datetime'];
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

    /** @return HasMany<PrescriptionSection, $this> */
    public function sections(): HasMany
    {
        return $this->hasMany(PrescriptionSection::class)->orderBy('position')->orderBy('id');
    }

    /** @return HasMany<PrescriptionActivity, $this> */
    public function activities(): HasMany
    {
        return $this->hasMany(PrescriptionActivity::class)->latest('id');
    }

    /** @param Builder<PrescriptionProfile> $query */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNull('superseded_at');
    }

    /** @return array{name: string, sections: list<array{name: string, items: list<array{type: string, recipe_id: int}>}>} */
    public function profileSnapshot(): array
    {
        $this->loadMissing('sections.items');

        return ['name' => $this->name, 'sections' => $this->sections->map(fn (PrescriptionSection $section): array => [
            'name' => $section->name,
            'items' => $section->items->map(fn (PrescriptionItem $item): array => [
                'type' => $item->bolus_id !== null ? 'bolus' : 'infusion',
                'recipe_id' => $item->bolus_id ?? $item->infusion_drug_id,
            ])->values()->all(),
        ])->values()->all()];
    }
}
