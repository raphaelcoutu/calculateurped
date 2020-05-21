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
        $canvas->page_text(150, 760, "Signature du prescripteur", null, 9, array(0.3,0.3,0.3));
        $canvas->page_text(330, 760, "No de permis", null, 9, array(0.3,0.3,0.3));
        $canvas->page_text(470, 760, "Date", null, 9, array(0.3,0.3,0.3));
        $canvas->page_text(530, 760, "hre", null, 9, array(0.3,0.3,0.3));
        $canvas->page_text(555, 760, "min", null, 9, array(0.3,0.3,0.3));
        $canvas->page_text(470, 745, "/        /                  :", null, 11, array(0.3,0.3,0.3));
        $canvas->page_line(80, 760, 400, 760, array(0,0,0),1);
        $canvas->page_line(430, 760, 580, 760, array(0,0,0),1);
        $canvas->page_text(40, 770, \Carbon\Carbon::now()->toDateTimeString(), null, 10, array(0, 0, 0));
        $canvas->page_text(520, 770, "Page {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));


        return $pdf->stream();

    }
}
