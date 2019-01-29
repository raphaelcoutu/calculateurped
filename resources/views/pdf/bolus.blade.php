@include('pdf.header', ['subheading' => 'Bolus'])
<table width="525" class="table-striped">
    <thead>
    <tr>
        <th colspan="2">Urgence/réanimation</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose</th>
        <th width="50">Volume</th>
        <th width="50">Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 1)->sortBy('name') as $bolus)
        <tr>
            <td width="110">{{$bolus->name}}</td>
            <td class="text-right" width="40"><i>{{$bolus->brand_name}}</i></td>
            <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/mL</td>
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$bolus->dose}}</td>
            <td class="text-right text-bold">{{$bolus->volume}}</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525" class="table-striped">
    <thead>
    <tr>
        <th colspan="2">Intubation séquence rapide</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose</th>
        <th width="50">Volume</th>
        <th width="50">Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 2)->sortBy('name') as $bolus)
        <tr>
            <td width="80">
                {{$bolus->name}}
            </td>
            <td class="text-right" width="70"><i>{{$bolus->brand_name}}</i></td>
            <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/mL</td>
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$bolus->dose}}</td>
            <td class="text-right text-bold">{{$bolus->volume}}</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>

<table width="525" class="table-striped-2">
    <thead>
    <tr>
        <th colspan="2">Autres médicaments</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose</th>
        <th width="50">Volume</th>
        <th width="50">Notes</th>
    </tr>
    </thead>
    <tbody>
    {{-- Hypertension intracranienne --}}
    <tr class="bg-white border-bottom">
        <td colspan="7"><i>HYPERTENSION INTRACRANIENNE</i></td>
    </tr>
    <tr class="bg-white">
        <td colspan="7"></td>
    </tr>
    @foreach($boluses->where('type', 3)->sortBy('name') as $bolus)
        <tr>
            <td class="indent">{{$bolus->name}}</td>
            <td></td>
            @if($bolus->commercial_concentration > 0)
                <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/mL</td>
            @else
                <td>-</td>
            @endif
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$bolus->dose}}</td>
            <td class="text-right text-bold">{{$bolus->volume}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            @isset($bolus->instructions)
                <td><u>Instructions:</u></td>
                <td colspan="4">{!! nl2br($bolus->instructions) !!}</td>
            @else
                <td colspan="5"></td>
            @endisset
        </tr>
    @endforeach

    {{-- Épilepsie --}}
    <tr class="bg-white border-bottom">
        <td colspan="7"><i>ÉPILEPSIE</i></td>
    </tr>
    <tr class="bg-white">
        <td colspan="7"></td>
    </tr>
    @foreach($boluses->where('type', 4)->sortBy('name') as $bolus)
        <tr>
            <td class="indent">{{$bolus->name}}</td>
            <td class="text-right"><i>{{$bolus->brand_name}}</i></td>
            @if($bolus->commercial_concentration > 0)
                <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/mL</td>
            @else
                <td>-</td>
            @endif
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$bolus->dose}}</td>
            <td class="text-right text-bold">{{$bolus->volume}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            @isset($bolus->instructions)
                <td><u>Instructions:</u></td>
                <td colspan="4">{!! nl2br($bolus->instructions) !!}</td>
            @else
                <td colspan="5"></td>
            @endisset
        </tr>
    @endforeach

    {{-- Anaphylaxie --}}
    <tr class="bg-white border-bottom">
        <td colspan="7"><i>ANAPHYLAXIE</i></td>
    </tr>
    <tr class="bg-white">
        <td colspan="7"></td>
    </tr>
    @foreach($boluses->where('type', 5)->sortBy('name') as $bolus)
        <tr>
            <td class="indent" width="80">{{$bolus->name}}</td>
            <td class="text-right" width="60"><i>{{$bolus->brand_name}}</i></td>
            @if($bolus->commercial_concentration > 0)
                <td>{{$bolus->commercial_concentration}} {{ $bolus->unit }}/mL</td>
            @else
                <td>-</td>
            @endif
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$bolus->dose}}</td>
            <td class="text-right text-bold">{{$bolus->volume}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
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
        <th width="80">Posologie</th>
        <th width="80">Dose</th>
        <th width="40">Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($boluses->where('type', 6) as $bolus)
        <tr>
            <td>{{$bolus->name}}</td>
            <td>{{$bolus->dosage}} {{ $bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$bolus->dose}}</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>

<p>** Doit être dilué et administré 15-30 minutes si le patient n'est pas en arrêt cardiorespiratoire.</p>