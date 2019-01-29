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
        @foreach($infusions->where('drug.type', 1)->sortBy('name') as $infusion)
            <tr>
                <td>
                    {{$infusion->drug->name}} [{{$infusion->drug->concentration}}]
                </td>
                <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/mL</td>
                <td>{{$infusion->total_volume}} mL</td>
                <td>{{$infusion->drug->dose_string}}</td>
                <td>
                    @if($infusion->isDebitMinLimited)
                        <small>MAX</small>
                    @endif
                    <strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif mL/h</strong>
                    @if($infusion->isDebitMaxLimited)
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
                <td colspan="3"><u>Recette:</u> {{$infusion->recipe}}</td>
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
        @foreach($infusions->where('drug.type', 2)->sortBy('name') as $infusion)
            <tr>
                <td>{{$infusion->drug->name}} [{{$infusion->drug->concentration}}]</td>
                <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/mL</td>
                <td>{{$infusion->total_volume}} mL</td>
                <td>{{$infusion->drug->dose_string}}</td>
                <td>
                    @if($infusion->isDebitMinLimited)
                        <small>MAX</small>
                    @endif
                    <strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif mL/h</strong>
                    @if($infusion->isDebitMaxLimited)
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
                @if($infusion->concentration === 100.00 && $infusion->concentration_unit === 'mU')
                    <td>
                        <span class="small">(0.1 U/mL)</span>
                    </td>
                    <td colspan="3"><u>Recette:</u> {{$infusion->recipe}}</td>
                @else
                    <td></td>
                    <td colspan="3"><u>Recette:</u> {{$infusion->recipe}}</td>
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
        @foreach($infusions->where('drug.type', 3)->sortBy('name') as $infusion)
            <tr>
                <td>{{$infusion->drug->name}} [{{$infusion->drug->concentration}}]</td>
                <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/mL</td>
                <td>{{$infusion->total_volume}} mL</td>
                <td>{{$infusion->drug->dose_string}}</td>
                <td>
                    @if($infusion->isDebitMinLimited)
                        <small>MAX</small>
                    @endif
                    <strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif mL/h</strong>
                    @if($infusion->isDebitMaxLimited)
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
                <td colspan="3"><u>Recette:</u> {{$infusion->recipe}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection