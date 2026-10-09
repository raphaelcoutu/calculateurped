<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\PrescriptionProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<PrescriptionProfile> */
class PrescriptionProfileFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['organization_id' => Organization::factory(), 'profile_id' => (string) Str::uuid(), 'version' => 1, 'name' => fake()->words(3, true), 'status' => 'draft'];
    }
}
