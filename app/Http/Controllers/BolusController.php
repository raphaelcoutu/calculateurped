<?php

namespace App\Http\Controllers;

use App\Models\Bolus;
use App\Models\CalculatedBolus;
use Inertia\Inertia;
use Inertia\Response;

class BolusController extends Controller
{
    public function __invoke(): Response
    {
        $weight = session('app.dosingWeight');

        $boluses = Bolus::weight($weight)->get();

        $calculated = collect();

        foreach ($boluses as $bolus) {
            $calculated->push(new CalculatedBolus($bolus, $weight));
        }

        return Inertia::render('calculator/bolus', [
            'weight' => $weight,
            'boluses' => $calculated->map(fn (CalculatedBolus $bolus): array => [
                'name' => $bolus->bolus->name,
                'brandName' => $bolus->bolus->brand_name,
                'asterisk' => $bolus->bolus->asterisk,
                'type' => $bolus->bolus->type,
                'dose' => $bolus->roundedDoseString,
                'volume' => $bolus->roundedVolumeString,
                'instructions' => $bolus->bolus->instructions,
            ])->values(),
        ]);
    }
}
