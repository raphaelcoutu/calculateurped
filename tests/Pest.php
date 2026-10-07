<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature');

pest()->extend(TestCase::class)
    ->in('Unit/CalculatedBolusTest.php');

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Unit/CalculatedInfusionTest.php');
