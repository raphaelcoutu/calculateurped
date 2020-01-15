<?php

namespace App\Http\Controllers;

use App\Bolus;
use App\CalculatedBolus;
use App\CalculatedInfusion;
use App\InfusionConcentration;
use App\WeightCategory;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $weight = session('app.weight');
        $calcBoluses = collect();
        $calcInfusions = collect();

        $pdf = \App::make('dompdf.wrapper');

        $boluses = Bolus::weight($weight)->get();

        foreach($boluses as $bolus) {
            $calcBoluses->push(new CalculatedBolus($bolus, $weight));
        }

        $infusions = InfusionConcentration::with('drug')->weight(WeightCategory::get($weight))->get();

        foreach($infusions as $infusion) {
            $calcInfusions->push(new CalculatedInfusion($infusion, $weight));
        }

        $pdf->loadView('pdf.main', compact('calcBoluses', 'calcInfusions'));

        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(40, 750, \Carbon\Carbon::now()->toDateTimeString(), null, 10, array(0, 0, 0));
        $canvas->page_text(520, 750, "Page {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));


        return $pdf->stream();

    }
}
