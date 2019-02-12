<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('web.index');
    }

    public function form()
    {
        $name = request('name') ?? "_______________________________";
        $age = request('age') ?? "____________________";
        $id = request('id') ?? "______________";
        $weight = request('weight');
        $dosingWeight = request('weight') < 100 ? request('weight') : '100';

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
                'weight' => $weight,
                'dosingWeight' => $dosingWeight
            ]
        ]);

        return redirect()->to('/bolus');
    }

    public function reset()
    {
        session(['form' => [], 'app' => []]);

        return redirect()->to('/');
    }
}
