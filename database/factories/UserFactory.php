<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Bolus::class, function(Faker $faker) {
    return [
        'name' => $faker->name,
        'asterisk' => $faker->boolean,
        'unit' => 'mg',
        'commercial_concentration' => 10,
        'dosage' => 1,
        'minimum_dose' => 0,
        'maximum_dose' => 0,
        'dose_precision' => 1,
        'volume_precision' => 1,
        'instructions' => $faker->sentence(),
        'type' => 1,
        'min_weight' => 0,
        'max_weight' => 999
    ];
});

$factory->define(App\InfusionDrug::class, function (Faker $faker) {
   return [
       'name' => $faker->name,
       'brand_name' => $faker->name,
       'concentration' => $faker->numberBetween(0.1, 100) . ' mg/mL',
       'debit_min' => 1,
       'debit_max' => 5,
       'debit_dose_unit' => 'mg',
       'debit_time_unit' => 'h',
       'debit_min_limit' => 0,
       'debit_max_limit' => 0,
       'debit_limit_unit' => '',
       'dosage_precision' => 1,
       'type' => 1
   ];
});

$factory->define(App\InfusionConcentration::class, function (Faker $faker) {
    return [
        'infusion_drug_id' => 1,
        'concentration' => 10,
        'concentration_unit' => 'mg',
        'instructions' => 'Recette',
        'total_volume' => 100,
        'weight_category' => 1
    ];
});
