<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrescriptionSection extends Model
{
    protected $fillable = ['prescription_profile_id', 'name', 'position'];

    /** @return BelongsTo<PrescriptionProfile, $this> */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(PrescriptionProfile::class, 'prescription_profile_id');
    }

    /** @return HasMany<PrescriptionItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class)->orderBy('position')->orderBy('id');
    }
}
