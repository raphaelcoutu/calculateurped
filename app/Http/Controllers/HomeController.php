<?php

namespace App\Http\Controllers;

use App\Bolus;
use App\InfusionConcentration;

class HomeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function form()
    {
        $name = request('name') ?? "________________________________________";
        $age = request('age') ?? "____________________";
        $id = request('id') ?? "______________";
        $weight = request('weight');

        session([
            'form' => [
                'name' => request('name'),
                'age' => request('age'),
                'id' => request('id'),
                'weight' => request('weight')
            ],
            'app' => [
                'name' => $name,
                'age' => $age,
                'id' => $id,
                'weight' => $weight
            ]
        ]);

        return redirect()->to('/bolus');
    }

    public function bolus()
    {
        $weight = session('app.weight');

        if(!is_numeric($weight) || $weight <= 0)
            return redirect()->to('/');

        $boluses = Bolus::all()->each(function ($bolus) use ($weight) {
            $bolus->dose = $bolus->getDoseString($weight);
            $bolus->volume = $bolus->getVolumeString($weight);
        });

        return view('bolus', compact('boluses', 'patientInfo'));
    }

    public function infusion()
    {
        $weight = session('app.weight');

        if(!is_numeric($weight) || $weight <= 0)
            return redirect()->to('/');

        $weightCategory = $this->findWeightCategory($weight);

        $infusions = InfusionConcentration::with('drug')
            ->where('weight_category', $weightCategory)->get()->each(function ($infusion) use ($weight) {
            $infusion->debit_min = $infusion->getDebitMinimal($weight);
            $infusion->debit_max = $infusion->getDebitMaximal($weight);
        });

        return view('infusion', compact('infusions'));
    }

    public function pdf()
    {
        $weight = session('app.weight');
        if(!is_numeric($weight) || $weight <= 0)
            return redirect()->to('/');

        $pdf = \App::make('dompdf.wrapper');

        $boluses = Bolus::all()->each(function ($bolus) use ($weight) {
            $bolus->dose = $bolus->getDoseString($weight);
            $bolus->volume = $bolus->getVolumeString($weight);
        });

        $weightCategory = $this->findWeightCategory($weight);

        $infusions = InfusionConcentration::with('drug')
            ->where('weight_category', $weightCategory)->get()->each(function ($infusion) use ($weight) {
                $infusion->debit_min = $infusion->getDebitMinimal($weight);
                $infusion->debit_max = $infusion->getDebitMaximal($weight);
            });

        $pdf->loadView('pdf.main', compact('boluses', 'infusions'));

        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(520, 750, "Page {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));


        return $pdf->stream();
    }

    private function findWeightCategory($weight)
    {
        if ($weight > 0 && $weight <= 5) {
            return 1;
        } else if ($weight > 5 && $weight <= 10) {
            return 2;
        } else if ($weight > 10 && $weight <= 15) {
            return 3;
        } else if ($weight > 15 && $weight < 35) {
            return 4;
        } else if ($weight >= 35) {
            return 5;
        } else {
            return 0;
        }
    }

    public function reset()
    {
        session(['form' => [], 'app' => []]);

        return redirect()->to('/');
    }
}
