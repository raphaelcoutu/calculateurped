<?php

namespace App\Http\Controllers;

use App\Models\CalculatedInfusion;
use App\Models\InfusionConcentration;
use App\WeightCategory;
use Inertia\Inertia;
use Inertia\Response;

class InfusionController extends Controller
{
    public function __invoke(): Response
    {
        $weight = session('app.dosingWeight');

        $weightCategory = WeightCategory::get($weight);

        $infusions = InfusionConcentration::with('drug')->weight($weightCategory)->get();

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
