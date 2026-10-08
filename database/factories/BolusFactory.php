<?php

namespace Database\Factories;

use App\Models\Bolus;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Bolus> */
class BolusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'organization_id' => Organization::factory(),
            'recipe_id' => (string) Str::uuid(),
            'version' => 1,
            'status' => 'published',
            'published_at' => now(),
            'asterisk' => $this->faker->boolean,
            'unit' => 'mg',
            'commercial_concentration' => 10,
            'dosage' => 1,
            'minimum_dose' => 0,
            'maximum_dose' => 0,
            'dose_precision' => 1,
            'volume_precision' => 1,
            'instructions' => $this->faker->sentence(),
            'type' => 1,
            'min_weight' => 0,
            'max_weight' => 999,
        ];
    }
}
