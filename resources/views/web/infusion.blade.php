@extends('layouts.app')

@section('content')
    @include('web.header', ['page' => 'infusion'])

    <h3 class="text-center">PERFUSIONS CONTINUES (doses de départ)</h3>
    <table class="table table-sm table-borderless table-green-2">
        <thead>
        <tr>
            <th width="25%">Sédation</th>
            <th width="10%">Concentration finale</th>
            <th width="10%">Volume total</th>
            <th width="20%">Dose (min-max)</th>
            <th width="20%">Débit (min-max)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($calculated->where('drug.type', 1)->sortBy('drug.name') as $infusion)
            <tr>
                <td>
                    {{$infusion->drug->name}} [{{$infusion->drug->concentration}}]
                </td>
                <td>{{$infusion->recipe->concentration}} {{$infusion->recipe->concentration_unit}}/mL</td>
                <td>{{$infusion->recipe->total_volume}} mL</td>
                <td>{{$infusion->dosageString}}</td>
                <td>
                    <strong>{{$infusion->rateString}}</strong>
                    @if($infusion->isRateMinLimited || $infusion->isRateMaxLimited)
                        <small>MAX</small>
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    @if($infusion->drug->brand_name)
                    <span class="small"><i>{{ $infusion->drug->brand_name }}</i></span>
                    @endif
                </td>
                <td></td>
                <td colspan="3"><u>Recette:</u> {{$infusion->recipe->instructions}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-sm table-borderless table-red-2">
        <thead>
        <tr>
            <th width="25%">Cardiovasculaire</th>
            <th width="10%">Concentration finale</th>
            <th width="10%">Volume total</th>
            <th width="20%">Dose (min-max)</th>
            <th width="20%">Débit (min-max)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($calculated->where('drug.type', 2)->sortBy('name') as $infusion)
            <tr>
                <td>{{$infusion->drug->name}} [{{$infusion->drug->concentration}}]</td>
                <td>{{$infusion->recipe->concentration}} {{$infusion->recipe->concentration_unit}}/mL</td>
                <td>{{$infusion->recipe->total_volume}} mL</td>
                <td>{{$infusion->dosageString}}</td>
                <td>
                    <strong>{{$infusion->rateString}}</strong>
                    @if($infusion->isRateMinLimited || $infusion->isRateMaxLimited)
                        <small>MAX</small>
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    @if($infusion->drug->brand_name)
                    <span class="small"><i>{{ $infusion->drug->brand_name }}</i></span>
                    @endif
                </td>
                @if($infusion->recipe->concentration === 100.00 && $infusion->recipe->concentration_unit === 'mU')
                    <td>
                        <span class="small">(0.1 U/mL)</span>
                    </td>
                    <td colspan="3"><u>Recette:</u> {{$infusion->recipe->instructions}}</td>
                @else
                    <td></td>
                    <td colspan="3"><u>Recette:</u> {{$infusion->recipe->instructions}}</td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-sm table-borderless table-yellow-2">
        <thead>
        <tr>
            <th width="25%">Autres médicaments</th>
            <th width="10%">Concentration finale</th>
            <th width="10%">Volume total</th>
            <th width="20%">Dose (min-max)</th>
            <th width="20%">Débit (min-max)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($calculated->where('drug.type', 3)->sortBy('name') as $infusion)
            <tr>
                <td>{{$infusion->drug->name}} [{{$infusion->drug->concentration}}]</td>
                <td>{{$infusion->recipe->concentration}} {{$infusion->recipe->concentration_unit}}/mL</td>
                <td>{{$infusion->recipe->total_volume}} mL</td>
                <td>{{$infusion->dosageString}}</td>
                <td>
                    <strong>{{$infusion->rateString}}</strong>
                    @if($infusion->isRateMinLimited || $infusion->isRateMaxLimited)
                        <small>MAX</small>
                    @endif
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    @if($infusion->drug->brand_name)
                    <span class="small"><i>{{ $infusion->drug->brand_name }}</i></span>
                    @endif
                </td>
                <td colspan="3"><u>Recette:</u> {{$infusion->recipe->instructions}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
