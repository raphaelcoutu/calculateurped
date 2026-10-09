<?php

namespace App\Http\Controllers;

use App\Models\CalculatedInfusion;
use App\Models\InfusionDrug;
use Inertia\Inertia;
use Inertia\Response;

class InfusionController extends Controller
{
    public function __invoke(): Response
    {
        $weight = session('app.dosingWeight');

        $infusions = InfusionDrug::preparationsForWeight((float) $weight);

        $calculated = collect();

        foreach ($infusions as $infusion) {
            $calculated->push(new CalculatedInfusion($infusion, $weight));
        }

        return Inertia::render('calculator/infusion', [
            'weight' => $weight,
            'infusions' => $calculated->map(fn (CalculatedInfusion $infusion): array => [
                'name' => $infusion->drug->name,
                'brandName' => $infusion->drug->brand_name,
                'type' => $infusion->drug->type,
                'concentration' => $infusion->recipe->concentration.' '.$infusion->recipe->concentration_unit,
                'dosage' => $infusion->dosageString,
                'rate' => $infusion->rateString,
                'instructions' => $infusion->recipe->instructions,
            ])->values(),
        ]);
    }
}
