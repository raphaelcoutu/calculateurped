<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('home/index', [
            'form' => [
                'name' => session('form.name', ''),
                'id' => session('form.id', ''),
            ],
        ]);
    }

    public function form()
    {
        $name = request('name') ?? '___________________________';
        $age = request('age') ?? '____________________';
        $id = request('id') ?? '______________';
        $weight = request('weight');
        $dosingWeight = request('weight') < 100 ? request('weight') : '100';
        $isWeightEstimated = request('estimated');

        session([
            'form' => [
                'name' => request('name'),
                'age' => request('age'),
                'id' => request('id'),
                'weight' => request('weight'),
                'isWeightEstimated' => request('estimated'),
            ],
            'app' => [
                'name' => $name,
                'age' => $age,
                'id' => $id,
                'weight' => $weight,
                'dosingWeight' => $dosingWeight,
                'isWeightEstimated' => $isWeightEstimated,
            ],
        ]);

        return redirect()
            ->to('/pdf');
    }

    public function reset()
    {
        session(['form' => [], 'app' => []]);

        return redirect()->to('/');
    }
}
