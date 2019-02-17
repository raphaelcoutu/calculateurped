<?php

namespace Tests\Unit;

use App\Bolus;
use App\CalculatedBolus;
use Tests\TestCase;

class CalculatedBolusTest extends TestCase
{
    private $bolus;
    private $shock;
    private $hypertonicSodium;

    protected function setUp()
    {
        parent::setUp();
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
            'volume_precision' => '1'
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
            'volume_precision' => '1'
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
            'volume_precision' => '0'
        ]);
    }

    /** @test */
    public function it_should_return_a_dose()
    {
        $calcBolus = new CalculatedBolus($this->bolus, 5);

        $this->assertEquals(25, $calcBolus->dose);
    }

    /** @test */
    public function it_should_return_maximum_dose_if_dose_exceeds_maximum()
    {
        $calcBolus = new CalculatedBolus($this->bolus, 100);

        $this->assertEquals(300, $calcBolus->dose);
    }

    /** @test */
    public function it_should_return_minimum_dose_if_dose_inferior_minimum()
    {
        $calcBolus = new CalculatedBolus($this->bolus, 1);

        $this->assertEquals(10, $calcBolus->dose);
    }

    /** @test */
    public function it_should_return_a_volume()
    {
        $calcBolus = new CalculatedBolus($this->bolus, 5.12);
        $this->assertEquals(0.512, $calcBolus->volume);
    }

    /** @test */
    public function it_should_return_a_volume_with_maximum_dose()
    {
        $calcBolus = new CalculatedBolus($this->bolus, 100);
        $this->assertEquals(6, $calcBolus->volume);
    }

    /** @test */
    public function it_should_return_a_volume_with_minimum_dose()
    {
        $calcBolus = new CalculatedBolus($this->bolus, 0.1);
        $this->assertEquals(0.2, $calcBolus->volume);
    }
    
    /** @test */
    public function it_should_not_return_volume_for_shock()
    {
        $calcBolus = new CalculatedBolus($this->shock, 5);
        $this->assertEquals(-1, $calcBolus->volume);
    }

    /** @test */
    public function it_should_return_a_rounded_dose_for_shock()
    {
        $calcBolus = new CalculatedBolus($this->shock, 5);
        $this->assertEquals(10, $calcBolus->roundedDose);
    }

    /** @test */
    public function it_should_return_a_volume_that_equals_dose_for_hypertonic_sodium()
    {
        $calcBolus = new CalculatedBolus($this->hypertonicSodium, 2.56);
        $this->assertEquals(7.68, $calcBolus->volume);
        $this->assertEquals(7.68, $calcBolus->dose);
    }

    /** @test */
    public function it_should_return_a_rounded_volume_that_equals__rounded_dose_for_hypertonic_sodium()
    {
        $calcBolus = new CalculatedBolus($this->hypertonicSodium, 2.56);
        $this->assertEquals(8, $calcBolus->roundedVolume);
        $this->assertEquals(8, $calcBolus->roundedDose);
    }

    /** @test */
    public function it_should_return_a_rounded_dose()
    {
        $this->bolus->volumePrecision = 1;
        $this->bolus->dosePrecision = 1;
        $calcBolus = new CalculatedBolus($this->bolus, 5.125);
        $this->assertEquals(25.5, $calcBolus->roundedDose);

        $this->bolus->volume_precision = 2;
        $this->bolus->dose_precision = 1;
        $calcBolus = new CalculatedBolus($this->bolus, 5.125);
        $this->assertEquals(0.51, $calcBolus->roundedVolume);
        $this->assertEquals(25.5, $calcBolus->roundedDose);

        $this->bolus->volume_precision = 1;
        $this->bolus->dose_precision = 2;
        $calcBolus = new CalculatedBolus($this->bolus, 5.125);
        $this->assertEquals(25.5, $calcBolus->roundedDose);

        $this->bolus->volume_precision = 2;
        $this->bolus->dose_precision = 2;
        $calcBolus = new CalculatedBolus($this->bolus, 5.125);
        $this->assertEquals(25.5, $calcBolus->roundedDose);
    }

    /** @test */
    public function volume_should_have_2_digits_precision_below_1_ml()
    {
        $bolus = factory(Bolus::class)->make(['commercial_concentration' => 33]);
        $calc = new CalculatedBolus($bolus, 5.1);

        $this->assertEquals(0.15, $calc->roundedVolume);
    }

    /** @test */
    public function volume_should_have_2_digits_precision_and_multiple_of_3_between_1_and_3_ml()
    {
        $bolus = factory(Bolus::class)->make(['commercial_concentration' => 13]);

        // 1.161...
        $calc = new CalculatedBolus($bolus, 15.1);
        $this->assertEquals(1.15, $calc->roundedVolume);

        // 1.1846...
        $calc = new CalculatedBolus($bolus, 15.4);
        $this->assertEquals(1.2, $calc->roundedVolume);
    }

    /** @test */
    public function volume_should_be_rounded_if_greater_than_3_ml()
    {
        $this->bolus->volume_precision = 1;
        $calcBolus = new CalculatedBolus($this->bolus, 40.12);

        // 4.012 mL
        $this->assertEquals(4.0, $calcBolus->roundedVolume);

        $this->bolus->volume_precision = 2;
        $calcBolus = new CalculatedBolus($this->bolus, 40.12);

        // 4.012 mL
        $this->assertEquals(4.01, $calcBolus->roundedVolume);
    }
}
