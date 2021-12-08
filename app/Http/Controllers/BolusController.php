<?php

namespace App\Http\Controllers;

use App\Models\Bolus;
use App\Models\CalculatedBolus;

class BolusController extends Controller
{
    public function __invoke()
    {
        $weight = session('app.dosingWeight');

        $boluses = Bolus::weight($weight)->get();

        $calculated = collect();

        foreach($boluses as $bolus) {
            $calculated->push(new CalculatedBolus($bolus, $weight));
        }

        return view('web.bolus', compact('calculated'));
    }
}
