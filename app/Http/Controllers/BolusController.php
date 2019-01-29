<?php

namespace App\Http\Controllers;

use App\Bolus;
use App\CalculatedBolus;
use Illuminate\Http\Request;

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

        return view('web.bolus', compact('calculated', 'patientInfo'));
    }
}
