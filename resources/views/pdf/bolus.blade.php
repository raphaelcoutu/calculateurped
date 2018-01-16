<h2>Bolus</h2>

<table width="525">
    <thead>
    <tr>
        <th width="125">Urgence/réanimation</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose Max</th>
        <th width="50">Dose</th>
        <th width="50">Volume</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 1) as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/ml</td>
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td>{{$bolus->maximum_dose_string}}</td>
            <td>{{$bolus->dose}}</td>
            <td>{{$bolus->volume}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525">
    <thead>
    <tr>
        <th width="125">Intubation séquence rapide</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose Max</th>
        <th width="50">Dose</th>
        <th width="50">Volume</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 2) as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/ml</td>
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td>{{$bolus->maximum_dose_string}}</td>
            <td>{{$bolus->dose}}</td>
            <td>{{$bolus->volume}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525">
    <thead>
    <tr>
        <th width="125">Autres médicaments</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose Max</th>
        <th width="50">Dose</th>
        <th width="50">Volume</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 3) as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            @if($bolus->commercial_concentration > 0)
                <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/ml</td>
            @else
                <td>-</td>
            @endif
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td>{{$bolus->maximum_dose_string}}</td>
            <td>{{$bolus->dose}}</td>
            <td>{{$bolus->volume}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525">
    <thead>
    <tr>
        <th width="125">Défibrillation</th>
        <th width="50">Posologie</th>
        <th width="50">Dose Max</th>
        <th width="50">Dose</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 4) as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td>{{$bolus->maximum_dose_string}}</td>
            <td>{{$bolus->dose}}</td>
        </tr>
    @endforeach
    </tbody>
</table>