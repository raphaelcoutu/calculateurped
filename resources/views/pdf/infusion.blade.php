@include('pdf.header', ['pageHeading' => 'Perfusions', 'pageSubheading' => 'Doses de départ'])
<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="120">Sédation</th>
        <th width="30">Concentration<br>finale</th>
        <th width="30">Volume total</th>
        <th width="90">Dose (min-max)</th>
        <th width="90">Débit (min-max)</th>
        <th width="30">Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcInfusions->where('drug.type', 1)->sortBy('drug.name') as $infusion)
        <tr>
            <td>{{$infusion->drug->name}} [{{$infusion->drug->concentration}}]</td>
            <td class="text-right text-bold">{{$infusion->recipe->concentration}} {{$infusion->recipe->concentration_unit}}/mL</td>
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

<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="95">Cardiovasculaire</th>
        <th width="30">Concentration<br>finale</th>
        <th width="30">Volume total</th>
        <th width="90">Dose (min-max)</th>
        <th width="90">Débit (min-max)</th>
        <th width="30">Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcInfusions->where('drug.type', 2)->sortBy('name') as $infusion)
        <tr>
            <td>{{$infusion->drug->name}} [{{$infusion->drug->concentration}}]</td>
            <td class="text-right text-bold">{{$infusion->recipe->concentration}} {{$infusion->recipe->concentration_unit}}/mL</td>
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
                <td colspan="4"><u>Recette:</u> {{$infusion->recipe->instructions}}</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="110">Autres médicaments</th>
        <th width="30">Concentration<br>finale</th>
        <th width="30">Volume total</th>
        <th width="90">Dose (min-max)</th>
        <th width="90">Débit (min-max)</th>
        <th width="30">Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcInfusions->where('drug.type', 3)->sortBy('name') as $infusion)
        <tr>
            <td>{{$infusion->drug->name}} [{{$infusion->drug->concentration}}]</td>
            <td class="text-right text-bold">{{$infusion->recipe->concentration}} {{$infusion->recipe->concentration_unit}}/mL</td>
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
