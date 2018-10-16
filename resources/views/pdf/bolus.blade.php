<h2>Bolus</h2>
<table width="525" class="table-striped-2">
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
    @foreach($boluses->where('type', 1)->sortBy('name') as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/mL</td>
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td>{{$bolus->maximum_dose_string}}</td>
            <td>{{$bolus->dose}}</td>
            <td>{{$bolus->volume}}</td>
        </tr>
        <tr>
            <td></td>
            @isset($bolus->instructions)
            <td><u>Instructions:</u></td>
                <td colspan="4">{{ $bolus->instructions }}</td>
            @else
                <td colspan="5"></td>
            @endisset
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525" class="table-striped">
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
    @foreach($boluses->where('type', 2)->sortBy('name') as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/mL</td>
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td>{{$bolus->maximum_dose_string}}</td>
            <td>{{$bolus->dose}}</td>
            <td>{{$bolus->volume}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="125">Hypertension intracrânienne</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose Max</th>
        <th width="50">Dose</th>
        <th width="50">Volume</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 3)->sortBy('name') as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            @if($bolus->commercial_concentration > 0)
                <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/mL</td>
            @else
                <td>-</td>
            @endif
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td>{{$bolus->maximum_dose_string}}</td>
            <td>{{$bolus->dose}}</td>
            <td>{{$bolus->volume}}</td>
        </tr>
        <tr>
            <td></td>
            @isset($bolus->instructions)
                <td><u>Instructions:</u></td>
                <td colspan="4">{{ $bolus->instructions }}</td>
            @else
                <td colspan="5"></td>
            @endisset
        </tr>
    @endforeach
    </tbody>
</table>

<div class="page-break"></div>
@include('pdf.header', compact('patient'))
<h2>Bolus (suite)</h2>

<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th width="125">Épilepsie</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose Max</th>
        <th width="50">Dose</th>
        <th width="50">Volume</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 4)->sortBy('name') as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            @if($bolus->commercial_concentration > 0)
                <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/mL</td>
            @else
                <td>-</td>
            @endif
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td>{{$bolus->maximum_dose_string}}</td>
            <td>{{$bolus->dose}}</td>
            <td>{{$bolus->volume}}</td>
        </tr>
        <tr>
            <td></td>
            @isset($bolus->instructions)
                <td><u>Instructions:</u></td>
                <td colspan="4">{!! nl2br($bolus->instructions) !!}</td>
            @else
                <td colspan="5"></td>
            @endisset
        </tr>
    @endforeach
    </tbody>
</table>
<table width="525">
    <thead>
    <tr>
        <th width="125">Anaphylaxie</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose Max</th>
        <th width="50">Dose</th>
        <th width="50">Volume</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 5)->sortBy('name') as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            @if($bolus->commercial_concentration > 0)
                <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/mL</td>
            @else
                <td>-</td>
            @endif
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td>{{$bolus->maximum_dose_string}}</td>
            <td>{{$bolus->dose}}</td>
            <td>{{$bolus->volume}}</td>
        </tr>
        <tr>
            <td></td>
            @isset($bolus->instructions)
                <td><u>Instructions:</u></td>
                <td colspan="4">{{ $bolus->instructions }}</td>
            @else
                <td colspan="5"></td>
            @endisset
        </tr>
    @endforeach
    </tbody>
</table>
<table width="525" class="table-striped">
    <thead>
    <tr>
        <th width="125">Défibrillation</th>
        <th width="50">Posologie</th>
        <th width="50">Dose Max</th>
        <th width="50">Dose</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 6) as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td>{{$bolus->maximum_dose_string}}</td>
            <td>{{$bolus->dose}}</td>
        </tr>
    @endforeach
    </tbody>
</table>