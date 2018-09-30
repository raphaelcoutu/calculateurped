@extends('layouts.app')

@section('content')
    @include('header', ['page' => 'infusion'])

    <h3 class="text-center">PERFUSIONS CONTINUES</h3>
    <table class="table table-striped">
        <thead>
        <tr>
            <th width="25%">Sédation</th>
            <th width="20%">Dose (min-max)<br>Débit (min-max)</th>
            <th width="10%">Concentration finale</th>
            <th width="25%">Recette</th>
            <th width="10%">Volume total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($infusions->where('drug.type', 1)->sortBy('name') as $infusion)
            <tr>
                <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
                <td>{{$infusion->drug->dose_string}}<br>
                    (<strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif ml/h</strong>)</td>
                <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/ml</td>
                <td>{{$infusion->recipe}}</td>
                <td>{{$infusion->total_volume}} ml</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-striped">
        <thead>
        <tr>
            <th width="25%">Cardiovasculaire</th>
            <th width="20%">Dose (min-max)<br>Débit (min-max)</th>
            <th width="10%">Concentration finale</th>
            <th width="25%">Recette</th>
            <th width="10%">Volume total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($infusions->where('drug.type', 2)->sortBy('name') as $infusion)
            <tr>
                <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
                <td>{{$infusion->drug->dose_string}}<br>
                    (<strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif ml/h</strong>)</td>
                <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/ml</td>
                <td>{{$infusion->recipe}}</td>
                <td>{{$infusion->total_volume}} ml</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-striped">
        <thead>
        <tr>
            <th width="25%">Autres médicaments</th>
            <th width="20%">Dose (min-max)<br>Débit (min-max)</th>
            <th width="10%">Concentration finale</th>
            <th width="25%">Recette</th>
            <th width="10%">Volume total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($infusions->where('drug.type', 3)->sortBy('name') as $infusion)
            <tr>
                <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
                <td>{{$infusion->drug->dose_string}}<br>
                    (<strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif ml/h</strong>)</td>
                <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/ml</td>
                <td>{{$infusion->recipe}}</td>
                <td>{{$infusion->total_volume}} ml</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection