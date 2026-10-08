<?php

use App\Models\Bolus;
use App\Models\CalculatedBolus;

beforeEach(function (): void {

    $this->bolus = Bolus::make([
        'name' => 'Amiodarone',
        'brand_name' => 'Cordarone',
        'asterisk' => false,
        'unit' => 'mg',
        'commercial_concentration' => 50.0,
        'dosage' => '5',
        'minimum_dose' => '10',
        'maximum_dose' => '300',
        'dose_precision' => '1',
        'volume_precision' => '1',
    ]);

    $this->shock = Bolus::make([
        'name' => 'Shock',
        'brand_name' => '',
        'asterisk' => false,
        'unit' => 'J',
        'commercial_concentration' => 0.0,
        'dosage' => '2',
        'minimum_dose' => '0',
        'maximum_dose' => '200',
        'dose_precision' => '1',
        'volume_precision' => '1',
    ]);

    $this->hypertonicSodium = Bolus::make([
        'name' => 'Sodium',
        'brand_name' => '',
        'asterisk' => false,
        'unit' => 'mL',
        'commercial_concentration' => 0.0,
        'dosage' => '3',
        'minimum_dose' => '0',
        'maximum_dose' => '250',
        'dose_precision' => '0',
        'volume_precision' => '0',
    ]);

});

it('should return a dose', function (): void {
    $calcBolus = new CalculatedBolus($this->bolus, 5);

    $this->assertEquals(25, $calcBolus->dose);

});

it('should return maximum dose if dose exceeds maximum', function (): void {
    $calcBolus = new CalculatedBolus($this->bolus, 100);

    $this->assertEquals(300, $calcBolus->dose);

});

it('should return minimum dose if dose inferior minimum', function (): void {
    $calcBolus = new CalculatedBolus($this->bolus, 1);

    $this->assertEquals(10, $calcBolus->dose);

});

it('should return a volume', function (): void {
    $calcBolus = new CalculatedBolus($this->bolus, 5.12);
    $this->assertEquals(0.512, $calcBolus->volume);

});

it('should return a volume with maximum dose', function (): void {
    $calcBolus = new CalculatedBolus($this->bolus, 100);
    $this->assertEquals(6, $calcBolus->volume);

});

it('should return a volume with minimum dose', function (): void {
    $calcBolus = new CalculatedBolus($this->bolus, 0.1);
    $this->assertEquals(0.2, $calcBolus->volume);

});

it('should not return volume for shock', function (): void {
    $calcBolus = new CalculatedBolus($this->shock, 5);
    $this->assertEquals(-1, $calcBolus->volume);

});

it('should return a rounded dose for shock', function (): void {
    $calcBolus = new CalculatedBolus($this->shock, 5);
    $this->assertEquals(10, $calcBolus->roundedDose);

});

it('should return a volume that equals dose for hypertonic sodium', function (): void {
    $calcBolus = new CalculatedBolus($this->hypertonicSodium, 2.56);
    $this->assertEquals(7.68, $calcBolus->volume);
    $this->assertEquals(7.68, $calcBolus->dose);

});

it('should return a rounded volume that equals  rounded dose for hypertonic sodium', function (): void {
    $calcBolus = new CalculatedBolus($this->hypertonicSodium, 2.56);
    $this->assertEquals(8, $calcBolus->roundedVolume);
    $this->assertEquals(8, $calcBolus->roundedDose);

});

it('should return a rounded dose', function (): void {
    $this->bolus->volumePrecision = 1;
    $this->bolus->dosePrecision = 1;
    $calcBolus = new CalculatedBolus($this->bolus, 5.125);
    $this->assertEquals(25.6, $calcBolus->roundedDose);

    $this->bolus->volume_precision = 2;
    $this->bolus->dose_precision = 1;
    $calcBolus = new CalculatedBolus($this->bolus, 5.125);
    $this->assertEquals(0.51, $calcBolus->roundedVolume);
    $this->assertEquals(25.6, $calcBolus->roundedDose);

    $this->bolus->volume_precision = 1;
    $this->bolus->dose_precision = 2;
    $calcBolus = new CalculatedBolus($this->bolus, 35.125);
    $this->assertEquals(3.5, $calcBolus->roundedVolume);
    $this->assertEquals(175.63, $calcBolus->roundedDose);

    $this->bolus->volume_precision = 2;
    $this->bolus->dose_precision = 2;
    $calcBolus = new CalculatedBolus($this->bolus, 35.125);
    $this->assertEquals(3.51, $calcBolus->roundedVolume);
    $this->assertEquals(175.63, $calcBolus->roundedDose);

});

it('volume should have 2 digits precision below 1 ml', function (): void {
    $bolus = $this->bolus->replicate();
    $bolus->commercial_concentration = 33;
    $bolus->dosage = 1;
    $bolus->minimum_dose = 0;
    $calc = new CalculatedBolus($bolus, 5.1);

    $this->assertEquals(0.15, $calc->roundedVolume);

});

it('volume should have 2 digits precision and multiple of 3 between 1 and 3 ml', function (): void {
    $bolus = $this->bolus->replicate();
    $bolus->commercial_concentration = 13;
    $bolus->dosage = 1;
    $bolus->minimum_dose = 0;

    // 1.161...
    $calc = new CalculatedBolus($bolus, 15.1);
    $this->assertEquals(1.15, $calc->roundedVolume);

    // 1.1846...
    $calc = new CalculatedBolus($bolus, 15.4);
    $this->assertEquals(1.2, $calc->roundedVolume);

});

it('volume should be rounded if greater than 3 ml', function (): void {
    $this->bolus->volume_precision = 1;
    $calcBolus = new CalculatedBolus($this->bolus, 40.12);

    // 4.012 mL
    $this->assertEquals(4.0, $calcBolus->roundedVolume);

    $this->bolus->volume_precision = 2;
    $calcBolus = new CalculatedBolus($this->bolus, 40.12);

    // 4.012 mL
    $this->assertEquals(4.01, $calcBolus->roundedVolume);

});
