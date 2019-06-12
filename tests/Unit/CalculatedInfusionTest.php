<?php

namespace Tests\Unit;

use App\CalculatedInfusion;
use App\InfusionConcentration;
use App\InfusionDrug;
use App\WeightCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculatedInfusionTest extends TestCase
{
    use RefreshDatabase;

    private $drug;
    private $concentration;

    protected function setUp()
    {
        parent::setUp();

        InfusionDrug::create([
            'name' => 'Dexmédétomidine',
            'brand_name' => 'Précédex',
            'concentration' => '100 mcg/mL',
            'debit_min' => '1',
            'debit_max' => '2',
            'debit_dose_unit' => 'mcg',
            'debit_time_unit' => 'min',
            'debit_min_limit' => '200',
            'debit_max_limit' => '400',
            'debit_limit_unit' => 'mcg',
            'dosage_precision' => 1,
            'type' => 1
        ]);

        InfusionConcentration::create([
            'infusion_drug_id' => 1,
            'concentration' => 4,
            'concentration_unit' => 'mcg',
            'instructions' => 'Recette',
            'total_volume' => 50,
            'weight_category' => 1
        ]);

        InfusionConcentration::create([
            'infusion_drug_id' => 1,
            'concentration' => 0.008,
            'concentration_unit' => 'mg',
            'instructions' => 'Recette',
            'total_volume' => 50,
            'weight_category' => 2
        ]);

        InfusionDrug::create([
            'name' => 'Propofol',
            'brand_name' => '',
            'concentration' => '10 mg/mL',
            'debit_min' => '1.2',
            'debit_max' => '4.8',
            'debit_dose_unit' => 'mg',
            'debit_time_unit' => 'h',
            'debit_min_limit' => '400',
            'debit_max_limit' => '400',
            'debit_limit_unit' => 'mg',
            'dosage_precision' => 1,
            'type' => 1
        ]);

        InfusionConcentration::create([
            'infusion_drug_id' => 2,
            'concentration' => 10,
            'concentration_unit' => 'mg',
            'instructions' => 'Recette',
            'total_volume' => 100,
            'weight_category' => 1
        ]);

        InfusionDrug::create([
            'name' => 'Insuline',
            'brand_name' => '',
            'concentration' => '100 unités/mL',
            'debit_min' => '0.1',
            'debit_max' => '0.0',
            'debit_dose_unit' => 'unité',
            'debit_time_unit' => 'min',
            'debit_min_limit' => '0',
            'debit_max_limit' => '0',
            'debit_limit_unit' => '',
            'dosage_precision' => 1,
            'type' => 1
        ]);

        InfusionConcentration::create([
            'infusion_drug_id' => 3,
            'concentration' => 100,
            'concentration_unit' => 'mU',
            'instructions' => 'Recette',
            'total_volume' => 100,
            'weight_category' => 1
        ]);
    }

    /** @test */
    public function it_calculates_min_rate()
    {
        $weight = 3;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)->first();

        $calc = new CalculatedInfusion($product, $weight);
        $this->assertEquals(45, $calc->minimalRate);

        $product = InfusionConcentration::with('drug')->weight($weightCategory)
            ->where('infusion_drug_id', 2)->first();
        $anotherCalc = new CalculatedInfusion($product, $weight);
        $this->assertEquals(0.36, $anotherCalc->minimalRate);
    }

    /** @test */
    public function it_calculates_max_rate()
    {
        $weight = 3;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)->first();

        $calc = new CalculatedInfusion($product, $weight);
        $this->assertEquals(90, $calc->maximalRate);

        $product = InfusionConcentration::with('drug')->weight($weightCategory)
            ->where('infusion_drug_id', 2)->first();
        $anotherCalc = new CalculatedInfusion($product, $weight);
        $this->assertEquals(1.44, $anotherCalc->maximalRate);
    }

    /** @test */
    public function it_calculates_a_limited_min_rate()
    {
        $weight = 7;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)->first();

        $calc = new CalculatedInfusion($product, $weight);

        $this->assertEquals(25, $calc->minimalRate);
    }

    /** @test */
    public function it_calculates_a_limited_max_rate()
    {
        $weight = 7;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)->first();

        $calc = new CalculatedInfusion($product, $weight);

        $this->assertEquals(50, $calc->maximalRate);
    }

    /** @test */
    public function it_converts_rate_to_dosage()
    {
        $weight = 3;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)->first();

        $calc = new CalculatedInfusion($product, $weight);
        $this->assertEquals(1, $calc->minimalDosage);
        $this->assertEquals(2, $calc->maximalDosage);

        $product = InfusionConcentration::with('drug')->weight($weightCategory)
            ->where('infusion_drug_id', 2)->first();
        $anotherCalc = new CalculatedInfusion($product, $weight);
        $this->assertEquals(1.2, $anotherCalc->minimalDosage);
        $this->assertEquals(4.8, $anotherCalc->maximalDosage);
    }

    /** @test */
    public function it_converts_rate_to_dosage_with_limited_rates()
    {
        $weight = 7;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)->first();

        $calc = new CalculatedInfusion($product, $weight);
        $this->assertEquals(0.5, $calc->minimalDosage);
        $this->assertEquals(1, $calc->maximalDosage);
    }

    /** @test */
    public function it_respects_dosage_precision()
    {
        $weight = 3;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)
            ->where('infusion_drug_id', 2)->first();

        $product->drug->dosage_precision = 0;

        $anotherCalc = new CalculatedInfusion($product, $weight);
        $this->assertEquals(1, $anotherCalc->minimalDosage);
        $this->assertEquals(5, $anotherCalc->maximalDosage);

        $product->drug->dosage_precision = 2;
        $anotherCalc = new CalculatedInfusion($product, $weight);
        $this->assertEquals(1.2, $anotherCalc->minimalDosage);
        $this->assertEquals(4.8, $anotherCalc->maximalDosage);
    }

    /** @test */
    public function insuline_max_rate_should_be_0()
    {
        $weight = 3;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)
            ->where('infusion_drug_id', 3)->first();

        $calc = new CalculatedInfusion($product, $weight);

        $this->assertEquals(0, $calc->maximalRate);
    }

    /** @test */
    public function it_creates_a_simple_dosage_string()
    {
        $weight = 3;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)->first();

        $calc = new CalculatedInfusion($product, $weight);
        $this->assertEquals('1 - 2 mcg/kg/min', $calc->dosageString);
    }

    /** @test */
    public function it_creates_a_dosage_string_when_there_is_not_max_rate()
    {
        $weight = 3;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)
            ->where('infusion_drug_id', 3)->first();

        $calc = new CalculatedInfusion($product, $weight);
        $this->assertEquals('0.1 unité/kg/min', $calc->dosageString);
    }

    /** @test */
    public function it_creates_an_adjusted_dosage_string_when_rate_limited()
    {
        $weight = 7;
        $weightCategory = WeightCategory::get($weight);
        $product = InfusionConcentration::with('drug')->weight($weightCategory)->first();

        $calc = new CalculatedInfusion($product, $weight);
        $this->assertEquals('0.5 - 1 mcg/kg/min', $calc->dosageString);
    }

    /** @test */
    public function it_calculates_units()
    {
        $drug = factory(InfusionDrug::class)->create([
            'debit_min' => 0.2,
            'debit_max' => 2,
            'debit_dose_unit' => 'mU',
            'debit_time_unit' => 'min',
            'debit_min_limit' => 1.2,
            'debit_max_limit' => 6,
            'debit_limit_unit' => 'unité'
        ]);
        $conc = factory(InfusionConcentration::class)->create([
            'concentration' => 100,
            'concentration_unit' => 'mU'
        ]);
        $drug->concentrations()->save($conc);

        $weight = 60;
        $calc = new CalculatedInfusion($conc, $weight);
        $this->assertEquals(7.2, $calc->minimalRate);
        $this->assertEquals(60, $calc->maximalRate);
        $this->assertEquals(0.2, $calc->minimalDosage);
        $this->assertEquals(1.7, $calc->maximalDosage);
    }

    /** @test */
    public function infusion_rate_always_has_2_digit_precision()
    {
        $drug = factory(InfusionDrug::class)->create();
        $conc = factory(InfusionConcentration::class)->create();
        $drug->concentrations()->save($conc);

        $weight = 3.75;
        $calc = new CalculatedInfusion($conc, $weight);
        $digits = strlen(substr(strrchr($calc->minimalRate, '.'), 1));
        $this->assertEquals(2, $digits);

        $weight = 7.5;
        $calc = new CalculatedInfusion($conc, $weight);
        $digits = strlen(substr(strrchr($calc->maximalRate, '.'), 1));
        $this->assertEquals(2, $digits);
    }
}
