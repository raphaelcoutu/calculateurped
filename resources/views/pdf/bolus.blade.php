@include('pdf.header', ['pageHeading' => 'Bolus'])
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
    @foreach($calcBoluses->where('bolus.type', 1)->sortBy('bolus.name') as $calcBolus)
        <tr>
            <td width="110">
                @if($calcBolus->bolus->asterisk)
                    <span>** </span>
                @endif
                {{$calcBolus->bolus->name}}
            </td>
            <td class="text-right" width="40"><i>{{$calcBolus->bolus->brand_name}}</i></td>
            <td><strong>{{$calcBolus->bolus->commercial_concentration}} {{ $calcBolus->bolus->unit }}/mL</strong></td>
            <td>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$calcBolus->roundedDoseString}}</td>
            <td class="text-right text-bold">{{$calcBolus->roundedVolumeString}}</td>
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
<p>** Doit être dilué et administré 15-30 minutes si le patient n'est pas en arrêt cardiorespiratoire.</p>
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
    @foreach($calcBoluses->where('bolus.type', 2)->sortBy('bolus.name') as $calcBolus)
        <tr>
            <td width="80">
                {{$calcBolus->bolus->name}}
            </td>
            <td class="text-right" width="70"><i>{{$calcBolus->bolus->brand_name}}</i></td>
            <td><strong>{{$calcBolus->bolus->commercial_concentration}} {{ $calcBolus->bolus->unit }}/mL</strong></td>
            <td>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$calcBolus->roundedDoseString}}</td>
            <td class="text-right text-bold">{{$calcBolus->roundedVolumeString}}</td>
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
     {{--Hypertension intracranienne--}}
    <tr class="bg-white border-bottom">
        <td colspan="7"><i>HYPERTENSION INTRACRANIENNE</i></td>
    </tr>
    <tr class="bg-white">
        <td colspan="7"></td>
    </tr>
    @foreach($calcBoluses->where('bolus.type', 3)->sortBy('bolus.name') as $calcBolus)
        <tr>
            <td class="indent">{{$calcBolus->bolus->name}}</td>
            <td></td>
            @if($calcBolus->bolus->commercial_concentration > 0)
                <td><strong>{{$calcBolus->bolus->commercial_concentration}} {{ $calcBolus->bolus->unit }}/mL</strong></td>
            @else
                <td>-</td>
            @endif
            <td>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$calcBolus->roundedDoseString}}</td>
            <td class="text-right text-bold">{{$calcBolus->roundedVolumeString}}</td>
            <td>
                @if($calcBolus->roundedDose === $calcBolus->bolus->maximum_dose)
                    <small><i>MAX</i></small>
                @elseif($calcBolus->roundedDose === $calcBolus->bolus->minimum_dose)
                    <small><i>MIN</i></small>
                @endif
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            @if(!empty($calcBolus->bolus->instructions))
                <td><u>Instructions:</u></td>
                <td colspan="4">{!! nl2br($calcBolus->bolus->instructions) !!}</td>
            @else
                <td colspan="5"></td>
            @endisset
        </tr>
    @endforeach

     {{--Épilepsie--}}
    <tr class="bg-white border-bottom">
        <td colspan="7"><i>ÉPILEPSIE</i></td>
    </tr>
    <tr class="bg-white">
        <td colspan="7"></td>
    </tr>
    @foreach($calcBoluses->where('bolus.type', 4)->sortBy('bolus.name') as $calcBolus)
        <tr>
            <td class="indent">{{$calcBolus->bolus->name}}</td>
            <td class="text-right"><i>{{$calcBolus->bolus->brand_name}}</i></td>
            @if($calcBolus->bolus->commercial_concentration > 0)
                <td><strong>{{$calcBolus->bolus->commercial_concentration}} {{ $calcBolus->bolus->unit }}/mL</strong></td>
            @else
                <td>-</td>
            @endif
            <td>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$calcBolus->roundedDoseString}}</td>
            <td class="text-right text-bold">{{$calcBolus->roundedVolumeString}}</td>
            <td>
                @if($calcBolus->roundedDose === $calcBolus->bolus->maximum_dose)
                    <small><i>MAX</i></small>
                @elseif($calcBolus->roundedDose === $calcBolus->bolus->minimum_dose)
                    <small><i>MIN</i></small>
                @endif
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            @if(!empty($calcBolus->bolus->instructions))
                <td><u>Instructions:</u></td>
                <td colspan="4">{!! nl2br($calcBolus->bolus->instructions) !!}</td>
            @else
                <td colspan="5"></td>
            @endisset
        </tr>
    @endforeach

     {{--Anaphylaxie--}}
    <tr class="bg-white border-bottom">
        <td colspan="7"><i>ANAPHYLAXIE</i></td>
    </tr>
    <tr class="bg-white">
        <td colspan="7"></td>
    </tr>
    @foreach($calcBoluses->where('bolus.type', 5)->sortBy('bolus.name') as $calcBolus)
        <tr>
            <td class="indent" width="80">{{$calcBolus->bolus->name}}</td>
            <td class="text-right" width="60"><i>{{$calcBolus->bolus->brand_name}}</i></td>
            @if($calcBolus->bolus->commercial_concentration > 0)
                <td><strong>{{$calcBolus->bolus->commercial_concentration}} {{ $calcBolus->bolus->unit }}/mL</strong></td>
            @else
                <td>-</td>
            @endif
            <td>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$calcBolus->roundedDoseString}}</td>
            <td class="text-right text-bold">{{$calcBolus->roundedVolumeString}}</td>
            <td>
                @if($calcBolus->roundedDose === $calcBolus->bolus->maximum_dose)
                    <small><i>MAX</i></small>
                @elseif($calcBolus->roundedDose === $calcBolus->bolus->minimum_dose)
                    <small><i>MIN</i></small>
                @endif
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            @if(!empty($calcBolus->bolus->instructions))
                <td><u>Instructions:</u></td>
                <td colspan="4">{{ $calcBolus->bolus->instructions }}</td>
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
    @foreach($calcBoluses->where('bolus.type', 6) as $calcBolus)
        <tr>
            <td>{{$calcBolus->bolus->name}}</td>
            <td class="text-center">{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right text-bold">{{$calcBolus->roundedDoseString}}</td>
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
