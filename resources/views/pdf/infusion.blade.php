@include('pdf.header', ['subheading' => 'Perfusions'])
<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="110">Sédation</th>
        <th width="90">Dose (min-max)</th>
        <th width="90">Débit (min-max)</th>
        <th width="30">Concentration<br>finale</th>
        <th width="30">Volume total</th>
        <th width="30">Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($infusions->where('drug.type', 1)->sortBy('name') as $infusion)
        <tr>
            <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
            <td class="text-center">{{$infusion->drug->dose_string}}</td>
            <td class="text-center"><strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif mL/h</strong></td>
            <td class="text-right">{{$infusion->concentration}} {{$infusion->concentration_unit}}/mL</td>

            <td style="text-align: right">{{$infusion->total_volume}} mL</td>
            <td></td>
        </tr>
        <tr>
            <td><i>{{$infusion->drug->brand_name}}</i></td>
            <td colspan="5"><u>Recette:</u> {{$infusion->recipe}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="95">Cardiovasculaire</th>
        <th width="90">Dose (min-max)</th>
        <th width="90">Débit (min-max)</th>
        <th width="30">Concentration<br>finale</th>
        <th width="30">Volume total</th>
        <th width="30">Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($infusions->where('drug.type', 2)->sortBy('name') as $infusion)
        <tr>
            <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
            <td class="text-center">{{$infusion->drug->dose_string}}</td>
            <td class="text-center"><strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif mL/h</strong></td>
            <td class="text-right">{{$infusion->concentration}} {{$infusion->concentration_unit}}/mL</td>
            <td style="text-align: right">{{$infusion->total_volume}} mL</td>
            <td></td>
        </tr>
        <tr>
            <td><i>{{$infusion->drug->brand_name}}</i></td>
            @if($infusion->concentration === 100.00 && $infusion->concentration_unit === 'mU')
                <td colspan="2"><u>Recette:</u> {{$infusion->recipe}}</td>
                <td class="text-right">(0.1 U/mL)</td>
                <td colspan="2"></td>
            @else
                <td colspan="5"><u>Recette:</u> {{$infusion->recipe}}</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="110">Autres médicaments</th>
        <th width="90">Dose (min-max)</th>
        <th width="90">Débit (min-max)</th>
        <th width="30">Concentration<br>finale</th>
        <th width="30">Volume total</th>
        <th width="30">Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($infusions->where('drug.type', 3)->sortBy('name') as $infusion)
        <tr>
            <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
            <td class="text-center">{{$infusion->drug->dose_string}}</td>
            <td class="text-center"><strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif mL/h</strong></td>
            <td class="text-right">{{$infusion->concentration}} {{$infusion->concentration_unit}}/mL</td>
            <td style="text-align: right">{{$infusion->total_volume}} mL</td>
            <td></td>
        </tr>
        <tr>
            <td><i>{{$infusion->drug->brand_name}}</i></td>
            <td colspan="5"><u>Recette:</u> {{$infusion->recipe}}</td>
        </tr>
    @endforeach
    </tbody>
</table>