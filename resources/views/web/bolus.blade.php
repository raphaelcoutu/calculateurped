@extends('layouts.app')

@section('content')
    @include('web.header', ['page' => 'bolus'])

    <h3 class="text-center">BOLUS</h3>
    <table class="table table-borderless table-red">
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
        @foreach($calculated->where('bolus.type', 1)->sortBy('bolus.name') as $calc)
            <tr>
                <td class="d-flex justify-content-between">
                    <span>
                        @if($calc->bolus->asterisk)
                            **
                        @endif
                        {{ $calc->bolus->name }}
                    </span>
                    @if($calc->bolus->brand_name)
                    <span class="small"><i>{{ $calc->bolus->brand_name }}</i></span>
                    @endif
                </td>
                <td class="font-weight-bold">{{ $calc->bolus->commercial_concentration }} {{ $calc->bolus->unit }}/mL</td>
                <td>{{ $calc->bolus->dosage }} {{ $calc->bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">
                    @if($calc->roundedDose === $calc->bolus->maximum_dose)
                        <small class="text-muted">MAX</small>
                    @elseif($calc->roundedDose === $calc->bolus->minimum_dose)
                        <small class="text-muted">MIN</small>
                    @endif
                    {{ $calc->roundedDoseString }}
                </td>
                <td class="text-right font-weight-bold">{{ $calc->roundedVolumeString }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p class="alert alert-warning">** Doit être dilué et administré sur 15-30 minutes si le patient n'est pas en arrêt cardiorespiratoire.</p>

    <table class="table table-borderless table-green">
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
        @foreach($calculated->where('bolus.type', 2)->sortBy('bolus.name') as $calc)
            <tr>
                <td class="d-flex justify-content-between">
                    <span>{{ $calc->bolus->name }}</span>
                    <span class="small"><i>{{ $calc->bolus->brand_name }}</i></span>
                </td>
                <td class="font-weight-bold">{{ $calc->bolus->commercial_concentration }} {{ $calc->bolus->unit }}/mL</td>
                <td>{{ $calc->bolus->dosage }} {{ $calc->bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">
                    @if($calc->roundedDose === $calc->bolus->maximum_dose)
                        <small class="text-muted">MAX</small>
                    @endif
                    {{ $calc->roundedDoseString }}
                </td>
                <td class="text-right font-weight-bold" class="text-right font-weight-bold">{{ $calc->roundedVolumeString }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-borderless table-blue-2">
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
            <td colspan="6" class="alert-dark"><i>Hypertension intracrânienne</i></td>
        </tr>
        <tr></tr>
        @foreach($calculated->where('bolus.type', 3)->sortBy('bolus.name') as $calc)
            <tr>
                <td class="d-flex justify-content-between">
                    <span>{{ $calc->bolus->name }}</span>
                    <span class="small"><i>{{ $calc->bolus->brand_name }}</i></span>
                </td>
                @if($calc->bolus->commercial_concentration > 0)
                    <td class="font-weight-bold">{{ $calc->bolus->commercial_concentration }} {{ $calc->bolus->unit }}/mL</td>
                @else
                    <td>-</td>
                @endif
                <td>{{ $calc->bolus->dosage }} {{ $calc->bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">
                    @if($calc->roundedDose === $calc->bolus->maximum_dose)
                        <small class="text-muted">MAX</small>
                    @endif
                    {{ $calc->roundedDoseString }}
                </td>
                <td class="text-right font-weight-bold">{{ $calc->roundedVolumeString }}</td>
            </tr>
            <tr>
                @isset($calc->bolus->instructions)
                    <td></td>
                    <td><u>Instructions:</u></td>
                    <td colspan="4">{!! nl2br($calc->bolus->instructions) !!}</td>
                @endisset
            </tr>
        @endforeach
        <tr>
            <td colspan="6" class="alert-dark"><i>Épilepsie</i></td>
        </tr>
        <tr></tr>
        @foreach($calculated->where('bolus.type', 4)->sortBy('bolus.name') as $calc)
            <tr>
                <td class="d-flex justify-content-between">
                    <span>{{ $calc->bolus->name }}</span>
                    <span class="small"><i>{{ $calc->bolus->brand_name }}</i></span>
                </td>
                @if($calc->bolus->commercial_concentration > 0)
                    <td class="font-weight-bold">{{ $calc->bolus->commercial_concentration }} {{ $calc->bolus->unit }}/mL</td>
                @else
                    <td>-</td>
                @endif
                <td>{{ $calc->bolus->dosage }} {{ $calc->bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">
                    @if($calc->roundedDose === $calc->bolus->maximum_dose)
                        <small class="text-muted">MAX</small>
                    @endif
                    {{ $calc->roundedDoseString }}
                </td>
                <td class="text-right font-weight-bold">{{ $calc->roundedVolumeString }}</td>
            </tr>
            <tr>
                @isset($calc->bolus->instructions)
                    <td></td>
                    <td><u>Instructions:</u></td>
                    <td colspan="4">{!! nl2br($calc->bolus->instructions) !!}</td>
                @endisset
            </tr>
        @endforeach
        <tr>
            <td colspan="6" class="alert-dark"><i>Anaphylaxie</i></td>
        </tr>
        <tr></tr>
        @foreach($calculated->where('bolus.type', 5)->sortBy('bolus.name') as $calc)
            <tr>
                <td class="d-flex justify-content-between">
                    <span>{{ $calc->bolus->name }}</span>
                    <span class="small"><i>{{ $calc->bolus->brand_name }}</i></span>
                </td>
                @if($calc->bolus->commercial_concentration > 0)
                    <td class="font-weight-bold">{{ $calc->bolus->commercial_concentration }} {{ $calc->bolus->unit }}/mL</td>
                @else
                    <td>-</td>
                @endif
                <td>{{ $calc->bolus->dosage }} {{ $calc->bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">
                    @if($calc->roundedDose === $calc->bolus->maximum_dose)
                        <small class="text-muted">MAX</small>
                    @endif
                    {{ $calc->roundedDoseString }}
                </td>
                <td class="text-right font-weight-bold">{{ $calc->roundedVolumeString }}</td>
            </tr>
            <tr>
                @isset($calc->bolus->instructions)
                    <td></td>
                    <td><u>Instructions:</u></td>
                    <td colspan="4">{{ $calc->bolus->instructions }}</td>
                @endisset
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-borderless table-orange">
        <thead>
        <tr>
            <th width="35%">Défibrillation</th>
            <th width="15%">Concentration commerciale</th>
            <th width="15%">Posologie</th>
            <th width="15%" class="text-right">Dose</th>
        </tr>
        </thead>
        <tbody>
        @foreach($calculated->where('bolus.type', 6)->sortByDesc('bolus.name') as $calc)
            <tr>
                <td>{{ $calc->bolus->name }}</td>
                <td>-</td>
                <td>{{ $calc->bolus->dosage }} {{ $calc->bolus->unit }}/kg</td>
                <td class="text-right font-weight-bold">
                    @if($calc->roundedDose === $calc->bolus->maximum_dose)
                        <small class="text-muted">MAX</small>
                    @endif
                    {{ $calc->roundedDoseString }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection