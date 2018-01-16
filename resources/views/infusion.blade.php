@extends('layouts.app')

@section('content')
    @include('header', ['page' => 'infusion'])
    <table class="table table-striped">
        <thead>
        <tr>
            <th width="25%">Perfusions : Médicaments d'urgence/réanimation</th>
            <th width="15%">Dose</th>
            <th width="25%">Débit</th>
            <th width="10%">Concentration finale</th>
            <th width="25%">Recette</th>
            <th width="10%">Volume total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($infusions as $infusion)
            <tr>
                <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
                <td>{{$infusion->drug->dose_string}}</td>
                <td>{{$infusion->drug->initial_debit_string}} = <strong>{{$infusion->debit}} ml/h</strong></td>
                <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/ml</td>
                <td>{{$infusion->recipe}}</td>
                <td>{{$infusion->total_volume}} ml</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection