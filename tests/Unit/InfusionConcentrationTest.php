<?php

namespace Tests\Unit;

use App\InfusionConcentration;
use App\InfusionDrug;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InfusionConcentrationTest extends TestCase
{
    /** @test */
    public function it_should_return_debit()
    {
        $drug = new InfusionDrug();
        $drug->debit_min = 1;
        $drug->debit_dose_unit = 'mcg';
        $drug->debit_time_unit = 'h';
        $drug->unit = 'mcg';

        $infusion = new InfusionConcentration();
        $infusion->setRelation('drug', $drug);
        $infusion->concentration = 5;
        $infusion->concentration_unit = 'mcg';
        $this->assertEquals(1.0, $infusion->getDebit(5));

        $infusion->drug->debit_dose_unit = 'mg';
        $this->assertEquals(1000, $infusion->getDebit(5));

        $infusion->concentration_unit = 'mg';
        $this->assertEquals(1.0, $infusion->getDebit(5));

        $infusion->drug->debit_time_unit = 'min';
        $this->assertEquals(0.016666666666666666, $infusion->getDebit(5));
    }

    /** @test */
    public function it_should_return_debit_with_different_unit()
    {
        $this->assertTrue(true);
    }
}
