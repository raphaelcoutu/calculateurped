@extends('layouts.app')

@section('content')
    @include('header', ['page' => 'bolus'])

    <h3 class="text-center">BOLUS</h3>
    <table class="table table-striped">
        <thead>
        <tr>
            <th width="35%">Médicaments réanimation cardiorespiratoire / PALS</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%">Dose Max</th>
            <th width="15%">Dose</th>
            <th width="15%">Volume</th>
        </tr>
        </thead>
        <tbody>
        @foreach($boluses->where('type', 1)->sortBy('name') as $bolus)
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
    <p>** Doit être dilué et administré lentement si le patient n'est pas en arrêt cardiorespiratoire.</p>

    <table class="table table-striped">
        <thead>
        <tr>
            <th width="35%">Médicaments intubation séquence rapide</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%">Dose Max</th>
            <th width="15%">Dose</th>
            <th width="15%">Volume</th>
        </tr>
        </thead>
        <tbody>
        @foreach($boluses->where('type', 2)->sortBy('name') as $bolus)
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
            <th width="35%">Autres médicaments</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%">Dose Max</th>
            <th width="15%">Dose</th>
            <th width="15%">Volume</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="6" class="alert-info"><strong>Hypertension intracrânienne</strong></td>
        </tr>
        @foreach($boluses->where('type', 3)->sortBy('name') as $bolus)
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
        <tr>
            <td colspan="6" class="alert-info"><strong>Épilepsie</strong></td>
        </tr>
        @foreach($boluses->where('type', 4)->sortBy('name') as $bolus)
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
        <tr>
            <td colspan="6" class="alert-info"><strong>Anaphylaxie</strong></td>
        </tr>
        @foreach($boluses->where('type', 5)->sortBy('name') as $bolus)
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
        @foreach($boluses->where('type', 6)->sortBy('name') as $bolus)
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