@include('pdf.header', ['pageHeading' => 'Perfusions', 'pageSubheading' => 'Doses de départ'])
<table width="540" class="table-striped-2">
    <thead>
    <tr>
        <th width="5">MD</th>
        <th width="120">SÉDATION</th>
        <th width="30">Concentration<br>finale</th>
        <th width="30">Volume total</th>
        <th width="90">Dose (min-max)</th>
        <th width="80">Débit (min-max)</th>
        <th width="40">Dose réelle</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcInfusions->where('drug.type', 1)->sortBy('drug.order') as $infusion)
        <tr>
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
        <tr>
            <td><i>{{$infusion->drug->brand_name}}</i></td>
            <td></td>
            <td colspan="4"><u>Recette:</u> {{$infusion->recipe->instructions}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table width="540" class="table-striped-2">
    <thead>
    <tr>
        <th width="5">MD</th>
        <th width="100">CARDIOVASCULAIRE</th>
        <th width="30">Concentration<br>finale</th>
        <th width="30">Volume total</th>
        <th width="90">Dose (min-max)</th>
        <th width="80">Débit (min-max)</th>
        <th width="40">Dose réelle</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcInfusions->where('drug.type', 2)->sortBy('drug.order') as $infusion)
        <tr>
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
        <tr>
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

<table width="540" class="table-striped-2">
    <thead>
    <tr>
        <th width="5">MD</th>
        <th width="110">AUTRES MÉDICAMENTS</th>
        <th width="30">Concentration<br>finale</th>
        <th width="30">Volume total</th>
        <th width="90">Dose (min-max)</th>
        <th width="80">Débit (min-max)</th>
        <th width="40">Dose réelle</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcInfusions->where('drug.type', 3)->sortBy('drug.order') as $infusion)
        <tr>
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
        <tr>
            <td><i>{{$infusion->drug->brand_name}}</i></td>
            <td></td>
            <td colspan="4"><u>Recette:</u> {{$infusion->recipe->instructions}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Défibrillation</h2>
<table width="540" class="table-striped">
    <thead>
    <tr>
        <th width="5">MD</th>
        <th width="175">DÉFIBRILLATION</th>
        <th width="120">Posologie</th>
        <th width="120" colspan="2">Dose</th>
    </tr>
    </thead>
    <tbody>
        @foreach($calcBoluses->where('bolus.type', 6) as $calcBolus)
        <tr>
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
