<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InfusionConcentrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'infusion_drug_id' => 1,
            'concentration' => 10,
            'concentration_unit' => 'mg',
            'instructions' => 'Recette',
            'total_volume' => 100,
            'weight_category' => 1
        ];
    }
}
