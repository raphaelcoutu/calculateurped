<?php

namespace Database\Factories;

use App\Models\InfusionDrug;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<InfusionDrug> */
class InfusionDrugFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'recipe_id' => (string) Str::uuid(),
            'status' => 'published',
            'published_at' => now(),
            'version' => 1,
            'name' => $this->faker->name,
            'brand_name' => $this->faker->name,
            'concentration' => $this->faker->numberBetween(0.1, 100).' mg/mL',
            'debit_min' => 1,
            'debit_max' => 5,
            'debit_dose_unit' => 'mg',
            'debit_time_unit' => 'h',
            'debit_min_limit' => 0,
            'debit_max_limit' => 0,
            'debit_limit_unit' => '',
            'dosage_precision' => 1,
            'type' => 1,
        ];
    }
}
