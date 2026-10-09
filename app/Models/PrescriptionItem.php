<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

/**
 * @property Bolus|null $bolus
 * @property InfusionDrug|null $infusion
 */
class PrescriptionItem extends Model
{
    protected $fillable = ['prescription_section_id', 'bolus_id', 'infusion_drug_id', 'position'];

    /** @return BelongsTo<PrescriptionSection, $this> */
    public function section(): BelongsTo
    {
        return $this->belongsTo(PrescriptionSection::class, 'prescription_section_id');
    }

    /** @return BelongsTo<Bolus, $this> */
    public function bolus(): BelongsTo
    {
        return $this->belongsTo(Bolus::class)->withTrashed();
    }

    /** @return BelongsTo<InfusionDrug, $this> */
    public function infusion(): BelongsTo
    {
        return $this->belongsTo(InfusionDrug::class, 'infusion_drug_id')->withTrashed();
    }

    public static function assertRecipeCanBeWithdrawn(string $column, int $recipeId): void
    {
        if (self::protectsRecipe($column, $recipeId)) {
            throw ValidationException::withMessages([
                'recipe' => 'Cette recette est utilisée par un profil publié. Dépubliez ce profil avant de retirer la recette.',
            ]);
        }
    }

    public static function protectsRecipe(string $column, int $recipeId): bool
    {
        return self::where($column, $recipeId)->whereHas('section.profile', fn ($query) => $query->published())->exists();
    }
}
