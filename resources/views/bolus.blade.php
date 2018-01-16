@extends('layouts.app')

@section('content')
    @include('header', ['page' => 'bolus'])
    <table class="table table-striped">
        <thead>
        <tr>
            <th width="35%">Bolus : Médicaments d'urgence/réanimation</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%">Dose Max</th>
            <th width="15%">Dose</th>
            <th width="15%">Volume</th>
        </tr>
        </thead>
        <tbody>
        @foreach($boluses->where('type', 1) as $bolus)
            <tr>
                <td>{{$bolus->name}}</td>
                <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/ml</td>
                <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
                <td>{{$bolus->maximum_dose_string}}</td>
                <td>{{$bolus->dose}}</td>
                <td>{{$bolus->volume}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-striped">
        <thead>
        <tr>
            <th width="35%">Bolus : Médicaments intubation séquence rapide</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%">Dose Max</th>
            <th width="15%">Dose</th>
            <th width="15%">Volume</th>
        </tr>
        </thead>
        <tbody>
        @foreach($boluses->where('type', 2) as $bolus)
            <tr>
                <td>{{$bolus->name}}</td>
                <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/ml</td>
                <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
                <td>{{$bolus->maximum_dose_string}}</td>
                <td>{{$bolus->dose}}</td>
                <td>{{$bolus->volume}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-striped">
        <thead>
        <tr>
            <th width="35%">Bolus : Autres médicaments</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%">Dose Max</th>
            <th width="15%">Dose</th>
            <th width="15%">Volume</th>
        </tr>
        </thead>
        <tbody>
        @foreach($boluses->where('type', 3) as $bolus)
            <tr>
                <td>{{$bolus->name}}</td>
                @if($bolus->commercial_concentration > 0)
                    <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/ml</td>
                @else
                    <td>-</td>
                @endif
                <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
                <td>{{$bolus->maximum_dose_string}}</td>
                <td>{{$bolus->dose}}</td>
                <td>{{$bolus->volume}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-striped">
        <thead>
        <tr>
            <th width="35%">Défibrillation</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%">Dose Max</th>
            <th width="15%">Dose</th>
        </tr>
        </thead>
        <tbody>
        @foreach($boluses->where('type', 4) as $bolus)
            <tr>
                <td>{{$bolus->name}}</td>
                <td>-</td>
                <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
                <td>{{$bolus->maximum_dose_string}}</td>
                <td>{{$bolus->dose}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection