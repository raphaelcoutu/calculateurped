<?php

namespace App\Http\Controllers;

use App\Concerns\RendersPrescriptionPdf;
use App\Models\Bolus;
use App\Models\CalculatedBolus;
use App\Models\CalculatedInfusion;
use App\Models\InfusionDrug;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    use RendersPrescriptionPdf;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $weight = session('app.weight');
        $calcBoluses = collect();
        $calcInfusions = collect();

        $boluses = Bolus::weight($weight)->get();

        foreach ($boluses as $bolus) {
            $calcBoluses->push(new CalculatedBolus($bolus, $weight));
        }

        $infusions = InfusionDrug::preparationsForWeight((float) $weight);

        foreach ($infusions as $infusion) {
            $calcInfusions->push(new CalculatedInfusion($infusion, $weight));
        }

        return $this->renderPrescriptionPdf(compact('calcBoluses', 'calcInfusions'));

    }
}
