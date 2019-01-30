<?php

namespace App\Http\Controllers;

use App\Bolus;
use App\CalculatedBolus;
use App\CalculatedInfusion;
use App\InfusionConcentration;
use App\WeightCategory;

class InfusionController extends Controller
{
    public function __invoke()
    {
        $weight = session('app.dosingWeight');

        $weightCategory = WeightCategory::get($weight);

        $infusions = InfusionConcentration::with('drug')->weight($weightCategory)->get();

        $calculated = collect();

        foreach($infusions as $infusion) {
            $calculated->push(new CalculatedInfusion($infusion, $weight));
        }

        return view('web.infusion', compact('calculated'));
    }
}
