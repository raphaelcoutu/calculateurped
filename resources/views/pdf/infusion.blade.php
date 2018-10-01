<h2>Perfusions</h2>

<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="100">Sédation</th>
        <th width="75">Dose (min-max)</th>
        <th width="75">Débit (min-max)</th>
        <th width="50">Concentration<br>finale</th>
        <th width="50">Volume total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($infusions->where('drug.type', 1)->sortBy('name') as $infusion)
        <tr>
            <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
            <td>{{$infusion->drug->dose_string}}</td>
            <td><strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif ml/h</strong></td>
            <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/ml</td>

            <td style="text-align: right">{{$infusion->total_volume}} ml</td>
        </tr>
        <tr>
            <td></td>
            <td class="text-right"><u>Recette:</u></td>
            <td colspan="3">{{$infusion->recipe}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="100">Cardiovasculaire</th>
        <th width="75">Dose (min-max)</th>
        <th width="75">Débit (min-max)</th>
        <th width="50">Concentration<br>finale</th>
        <th width="50">Volume total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($infusions->where('drug.type', 2)->sortBy('name') as $infusion)
        <tr>
            <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
            <td>{{$infusion->drug->dose_string}}</td>
            <td><strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif ml/h</strong></td>
            <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/ml</td>

            <td style="text-align: right">{{$infusion->total_volume}} ml</td>
        </tr>
        <tr>
            <td></td>
            <td class="text-right"><u>Recette:</u></td>
            <td colspan="3">{{$infusion->recipe}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="100">Autres médicaments</th>
        <th width="75">Dose (min-max)</th>
        <th width="75">Débit (min-max)</th>
        <th width="50">Concentration<br>finale</th>
        <th width="50">Volume total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($infusions->where('drug.type', 3)->sortBy('name') as $infusion)
        <tr>
            <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
            <td>{{$infusion->drug->dose_string}}</td>
            <td><strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif ml/h</strong></td>
            <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/ml</td>

            <td style="text-align: right">{{$infusion->total_volume}} ml</td>
        </tr>
        <tr>
            <td></td>
            <td class="text-right"><u>Recette:</u></td>
            <td colspan="3">{{$infusion->recipe}}</td>
        </tr>
    @endforeach
    </tbody>
</table>