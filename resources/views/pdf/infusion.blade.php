@include('pdf.header')
<h2 class="page-heading">Perfusions</h2>
<p class="page-subheading"><i>Doses de départ</i></p>
<table style="width: 725px" class="table mt-10">
    <thead>
    <tr>
        <th style="width: 25px">MD</th>
        <th>SÉDATION</th>
        <th>Concentration<br>finale</th>
        <th>Volume total</th>
        <th>Dose (min-max)</th>
        <th>Débit (min-max)</th>
        <th>Dose réelle</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcInfusions->where('drug.type', 1)->sortBy('drug.order') as $infusion)
        <tr @class(['striped' => $loop->index % 2])>
            <td class="checkbox" rowspan="2"></td>
            <td>{{$infusion->drug->name}} [{{$infusion->drug->concentration}}]</td>
            <td class="text-right"><strong>{{$infusion->recipe->concentration}} {{$infusion->recipe->concentration_unit}}/mL</strong></td>
            <td style="text-align: right">{{$infusion->recipe->total_volume}} mL</td>
            <td class="text-center">{{$infusion->dosageString}}</td>
            <td class="text-center"><strong>{{$infusion->rateString}}</strong></td>
            <td>
                @if($infusion->isRateMinLimited || $infusion->isRateMaxLimited)
                    <small><i>MAX</i></small>
                @endif
            </td>
        </tr>
        <tr @class(['striped' => $loop->index % 2])>
            <td><i>{{$infusion->drug->brand_name}}</i></td>
            <td></td>
            <td colspan="4"><u>Recette:</u> {{$infusion->recipe->instructions}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table style="width: 725px" class="table mt-10">
    <thead>
    <tr>
        <th style="width: 25px">MD</th>
        <th>CARDIOVASCULAIRE</th>
        <th>Concentration<br>finale</th>
        <th>Volume total</th>
        <th>Dose (min-max)</th>
        <th>Débit (min-max)</th>
        <th>Dose réelle</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcInfusions->where('drug.type', 2)->sortBy('drug.order') as $infusion)
        <tr @class(['striped' => $loop->index % 2])>
            <td class="checkbox" rowspan="2"></td>
            <td>{{$infusion->drug->name}} [{{$infusion->drug->concentration}}]</td>
            <td class="text-right"><strong>{{$infusion->recipe->concentration}} {{$infusion->recipe->concentration_unit}}/mL</strong></td>
            <td style="text-align: right">{{$infusion->recipe->total_volume}} mL</td>
            <td class="text-center">{{$infusion->dosageString}}</td>
            <td class="text-center"><strong>{{$infusion->rateString}}</strong></td>
            <td>
                @if($infusion->isRateMinLimited || $infusion->isRateMaxLimited)
                    <small><i>MAX</i></small>
                @endif
            </td>
        </tr>
        <tr @class(['striped' => $loop->index % 2])>
            <td><i>{{$infusion->drug->brand_name}}</i></td>
            @if($infusion->recipe->concentration === 100.00 && $infusion->recipe->concentration_unit === 'mU')
                <td class="text-right">(0.1 U/mL)</td>
                <td colspan="4"><u>Recette:</u> {{$infusion->recipe->instructions}}</td>
            @else
                <td></td>
                <td colspan="4"><u>Recette:</u> {!! nl2br(e($infusion->recipe->instructions)) !!}</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>

<table style="width: 725px" class="table mt-10">
    <thead>
    <tr>
        <th style="width: 25px">MD</th>
        <th>AUTRES MÉDICAMENTS</th>
        <th>Concentration<br>finale</th>
        <th>Volume total</th>
        <th>Dose (min-max)</th>
        <th>Débit (min-max)</th>
        <th>Dose réelle</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcInfusions->where('drug.type', 3)->sortBy('drug.order') as $infusion)
        <tr @class(['striped' => $loop->index % 2])>
            <td class="checkbox" rowspan="2"></td>
            <td>{{$infusion->drug->name}} [{{$infusion->drug->concentration}}]</td>
            <td class="text-right"><strong>{{$infusion->recipe->concentration}} {{$infusion->recipe->concentration_unit}}/mL</strong></td>
            <td class="text-right">{{$infusion->recipe->total_volume}} mL</td>
            <td class="text-center">{{$infusion->dosageString}}</td>
            <td class="text-center"><strong>{{$infusion->rateString}}</strong></td>
            <td>
                @if($infusion->isRateMinLimited || $infusion->isRateMaxLimited)
                    <small><i>MAX</i></small>
                @endif
            </td>
        </tr>
        <tr @class(['striped' => $loop->index % 2])>
            <td><i>{{$infusion->drug->brand_name}}</i></td>
            <td></td>
            <td colspan="4"><u>Recette:</u> {{$infusion->recipe->instructions}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table style="width: 725px" class="table mt-10">
    <thead>
    <tr>
        <th style="width: 25px">MD</th>
        <th>DÉFIBRILLATION</th>
        <th>Posologie</th>
        <th colspan="2">Dose</th>
    </tr>
    </thead>
    <tbody>
        @foreach($calcBoluses->where('bolus.type', 6) as $calcBolus)
        <tr @class(['striped' => $loop->index % 2])>
            <td class="checkbox"></td>
            <td>{{$calcBolus->bolus->name}}</td>
            <td class="text-center"><strong>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</strong></td>
            <td class="text-right">{{$calcBolus->roundedDoseString}}</td>
            <td>
                @if($calcBolus->roundedDose === $calcBolus->bolus->maximum_dose)
                    <small><i>MAX</i></small>
                @elseif($calcBolus->roundedDose === $calcBolus->bolus->minimum_dose)
                    <small><i>MIN</i></small>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
