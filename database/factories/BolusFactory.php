<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BolusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
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
            'max_weight' => 999
        ];
    }
}
