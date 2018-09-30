<h2>Perfusions</h2>

<table width="525">
    <thead>
    <tr>
        <th width="130">Médicaments<br>d'urgence/réanimation</th>
        <th width="90">Dose (min-max)<br>Débit (min-max)</th>
        <th width="30">Concentration<br>finale</th>
        <th width="140">Recette</th>
        <th width="40">Volume total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($infusions as $infusion)
        <tr>
            <td>{{$infusion->drug->name}} {{$infusion->drug->concentration}}</td>
            <td>{{$infusion->drug->dose_string}}<br>
                (<strong>{{$infusion->debit_min}}@if($infusion->debit_max)-{{$infusion->debit_max}}@endif ml/h</strong>)</td>
            <td>{{$infusion->concentration}} {{$infusion->concentration_unit}}/ml</td>
            <td>{{$infusion->recipe}}</td>
            <td style="text-align: right">{{$infusion->total_volume}} ml</td>
        </tr>
    @endforeach
    </tbody>
</table>