<?php

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    protected $fillable = ['name', 'logo_path', 'default_prescription_profile_id'];

    protected function casts(): array
    {
        return ['default_prescription_profile_id' => 'integer'];
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** @return HasMany<Bolus, $this> */
    public function boluses(): HasMany
    {
        return $this->hasMany(Bolus::class);
    }
}
