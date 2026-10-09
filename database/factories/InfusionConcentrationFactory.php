<?php

namespace Database\Factories;

use App\Models\InfusionConcentration;
use App\Models\InfusionDrug;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<InfusionConcentration> */
class InfusionConcentrationFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'infusion_drug_id' => InfusionDrug::factory(),
            'concentration' => 10,
            'concentration_unit' => 'mg',
            'instructions' => 'Recette',
            'total_volume' => 100,
            'weight_category' => 1,
        ];
    }
}
