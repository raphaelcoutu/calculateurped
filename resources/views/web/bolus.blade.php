@extends('layouts.app')

@section('content')
    @include('web.header', ['page' => 'bolus'])

    <h3 class="text-center">BOLUS</h3>
    <table class="table table-borderless table-striped-2">
        <thead>
        <tr>
            <th width="35%">Médicaments réanimation cardiorespiratoire / PALS</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%" class="text-right">Dose</th>
            <th width="15%" class="text-right">Volume</th>
        </tr>
        </thead>
        <tbody>
        @foreach($boluses->where('type', 1)->sortBy('name') as $bolus)
            <tr>
                <td class="d-flex justify-content-between">
                    <span>{{ $bolus->name }}</span>
                    @if($bolus->brand_name)
                    <span class="small"><i>{{ $bolus->brand_name }}</i></span>
                    @endif
                </td>
                <td>{{ $bolus->commercial_concentration }} {{ $bolus->unit }}/mL</td>
                <td>{{ $bolus->dosage }} {{ $bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">
                    {{ $bolus->doseString }}
                </td>
                <td class="text-right font-weight-bold">{{ $bolus->volumeString }}</td>
            </tr>
            <tr>
                @isset($bolus->instructions)
                    <td></td>
                    <td><u>Instructions:</u></td>
                    <td colspan="4">{{ $bolus->instructions }}</td>
                @endisset
            </tr>
        @endforeach
        </tbody>
    </table>

    <p class="alert alert-warning">** Doit être dilué et administré sur 15-30 minutes si le patient n'est pas en arrêt cardiorespiratoire.</p>

    <table class="table table-borderless table-striped">
        <thead>
        <tr>
            <th width="35%">Médicaments intubation séquence rapide</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%" class="text-right">Dose</th>
            <th width="15%" class="text-right">Volume</th>
        </tr>
        </thead>
        <tbody>
        @foreach($boluses->where('type', 2)->sortBy('name') as $bolus)
            <tr>
                <td class="d-flex justify-content-between">
                    <span>{{ $bolus->name }}</span>
                    <span class="small"><i>{{ $bolus->brand_name }}</i></span>
                </td>
                <td>{{ $bolus->commercial_concentration }} {{ $bolus->unit }}/mL</td>
                <td>{{ $bolus->dosage }} {{ $bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">{{ $bolus->doseString }}</td>
                <td class="text-right font-weight-bold" class="text-right font-weight-bold">{{ $bolus->volumeString }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-borderless table-striped-2">
        <thead>
        <tr>
            <th width="35%">Autres médicaments</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%" class="text-right">Dose</th>
            <th width="15%" class="text-right">Volume</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="6" class="alert-info"><strong>Hypertension intracrânienne</strong></td>
        </tr>
        @foreach($boluses->where('type', 3)->sortBy('name') as $bolus)
            <tr>
                <td class="d-flex justify-content-between">
                    <span>{{ $bolus->name }}</span>
                    <span class="small"><i>{{ $bolus->brand_name }}</i></span>
                </td>
                @if($bolus->commercial_concentration > 0)
                    <td>{{ $bolus->commercial_concentration }} {{ $bolus->unit }}/mL</td>
                @else
                    <td>-</td>
                @endif
                <td>{{ $bolus->dosage }} {{ $bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">{{ $bolus->doseString }}</td>
                <td class="text-right font-weight-bold">{{ $bolus->volumeString }}</td>
            </tr>
            <tr>
                @isset($bolus->instructions)
                    <td></td>
                    <td><u>Instructions:</u></td>
                    <td colspan="4">{{ $bolus->instructions }}</td>
                @endisset
            </tr>
        @endforeach
        <tr>
            <td colspan="6" class="alert-info"><strong>Épilepsie</strong></td>
        </tr>
        @foreach($boluses->where('type', 4)->sortBy('name') as $bolus)
            <tr>
                <td class="d-flex justify-content-between">
                    <span>{{ $bolus->name }}</span>
                    <span class="small"><i>{{ $bolus->brand_name }}</i></span>
                </td>
                @if($bolus->commercial_concentration > 0)
                    <td>{{ $bolus->commercial_concentration }} {{ $bolus->unit }}/mL</td>
                @else
                    <td>-</td>
                @endif
                <td>{{ $bolus->dosage }} {{ $bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">{{ $bolus->doseString }}</td>
                <td class="text-right font-weight-bold">{{ $bolus->volumeString }}</td>
            </tr>
            <tr>
                @isset($bolus->instructions)
                    <td></td>
                    <td><u>Instructions:</u></td>
                    <td colspan="4">{!! nl2br($bolus->instructions) !!}</td>
                @endisset
            </tr>
        @endforeach
        <tr>
            <td colspan="6" class="alert-info"><strong>Anaphylaxie</strong></td>
        </tr>
        @foreach($boluses->where('type', 5)->sortBy('name') as $bolus)
            <tr>
                <td class="d-flex justify-content-between">
                    <span>{{ $bolus->name }}</span>
                    <span class="small"><i>{{ $bolus->brand_name }}</i></span>
                </td>
                @if($bolus->commercial_concentration > 0)
                    <td>{{ $bolus->commercial_concentration }} {{ $bolus->unit }}/mL</td>
                @else
                    <td>-</td>
                @endif
                <td>{{ $bolus->dosage }} {{ $bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">{{ $bolus->doseString }}</td>
                <td class="text-right font-weight-bold">{{ $bolus->volumeString }}</td>
            </tr>
            <tr>
                @isset($bolus->instructions)
                    <td></td>
                    <td><u>Instructions:</u></td>
                    <td colspan="4">{{ $bolus->instructions }}</td>
                @endisset
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
            <th width="15%" class="text-right">Dose</th>
        </tr>
        </thead>
        <tbody>
        @foreach($boluses->where('type', 6)->sortByDesc('name') as $bolus)
            <tr>
                <td>{{ $bolus->name }}</td>
                <td>-</td>
                <td>{{ $bolus->dosage }} {{ $bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">{{ $bolus->doseString }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection