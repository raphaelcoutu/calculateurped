@include('pdf.header')
<h2 class="page-heading">Bolus</h2>
<table style="width: 725px" class="table">
    <thead>
    <tr>
        <th style="width: 25px">MD</th>
        <th style="width: auto" colspan="2">URGENCE/RÉANIMATION</th>
        <th style="width: 100px">Concentration<br>commerciale</th>
        <th>Posologie</th>
        <th>Dose</th>
        <th style="width: 75px" colspan="2">Volume</th>
    </tr>
    </thead>
    <tbody>
        <tr class="table-subsection">
            <td colspan="8" class="text-center"><i>IV DIRECT</i></td>
        </tr>
    @foreach($calcBoluses->where('bolus.type', 1)->sortBy('bolus.name') as $calcBolus)
        <tr @class(['striped' => $loop->index % 2])>
            <td class="checkbox"></td>
            {{--Si on a pas de nom commercial, on fusionne les colonnes--}}
            @if(isset($calcBolus->bolus->brand_name) && !empty($calcBolus->bolus->brand_name))
                <td>
                    @if($calcBolus->bolus->asterisk)
                        <span>** <sup>voir note</sup></span>
                    @endif
                    {{$calcBolus->bolus->name}}
                </td>
                <td class="text-right"><i>{{$calcBolus->bolus->brand_name}}</i></td>
            @else
                <td colspan="2">
                    @if($calcBolus->bolus->asterisk)
                        <span>** <sup>voir note</sup></span>
                    @endif
                    {{$calcBolus->bolus->name}}
                </td>
            @endif
            <td><strong>{{$calcBolus->bolus->commercial_concentration}} {{ $calcBolus->bolus->unit }}/mL</strong></td>
            <td>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right"><strong>{{$calcBolus->roundedDoseString}}</strong></td>
            <td class="text-right"><strong>{{$calcBolus->roundedVolumeString}}</strong></td>
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
@if(session('app.weight') <= 15)
    <p class="note">** Doit être dilué dans 50 mL et administré sur 15-30 minutes si le patient n'est pas en arrêt cardiorespiratoire.</p>
@else
    <p class="note">** Doit être dilué dans 100 mL et administré sur 15-30 minutes si le patient n'est pas en arrêt cardiorespiratoire.</p>
@endif
<table style="width: 725px" class="table">
    <thead>
    <tr>
        <th style="width: 25px">MD</th>
        <th colspan="2">INTUBATION SÉQUENCE RAPIDE</th>
        <th style="width: 100px">Concentration<br>commerciale</th>
        <th>Posologie</th>
        <th>Dose</th>
        <th style="width: 75px" colspan="2">Volume</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcBoluses->where('bolus.type', 2)->sortBy('bolus.name') as $calcBolus)
        <tr @class(['striped' => $loop->index % 2])>
            <td class="checkbox"></td>
            <td>
                {{$calcBolus->bolus->name}}
            </td>
            <td class="text-right"><i>{{$calcBolus->bolus->brand_name}}</i></td>
            <td><strong>{{$calcBolus->bolus->commercial_concentration}} {{ $calcBolus->bolus->unit }}/mL</strong></td>
            <td>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right"><strong>{{$calcBolus->roundedDoseString}}</strong></td>
            <td class="text-right"><strong>{{$calcBolus->roundedVolumeString}}</strong></td>
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

<table style="width: 725px" class="table mt-10">
    <thead>
    <tr>
        <th style="width: 25px">MD</th>
        <th colspan="2">AUTRES MÉDICAMENTS</th>
        <th>Concentration<br>commerciale</th>
        <th>Posologie</th>
        <th>Dose</th>
        <th colspan="2">Volume</th>
    </tr>
    </thead>
    <tbody>
    <tr class="table-subsection">
        <td colspan="8"><i>HYPERTENSION INTRACRANIENNE</i></td>
    </tr>
    @foreach($calcBoluses->where('bolus.type', 3)->sortBy('bolus.name') as $calcBolus)
        <tr @class(['striped' => $loop->index % 2])>
            <td class="checkbox" rowspan="2"></td>
            <td>{{$calcBolus->bolus->name}}</td>
            <td></td>
            @if($calcBolus->bolus->commercial_concentration > 0)
                <td><strong>{{$calcBolus->bolus->commercial_concentration}} {{ $calcBolus->bolus->unit }}/mL</strong>
                </td>
            @else
                <td>-</td>
            @endif
            <td>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right"><strong>{{$calcBolus->roundedDoseString}}</strong></td>
            <td class="text-right"><strong>{{$calcBolus->roundedVolumeString}}</strong></td>
            <td>
                @if($calcBolus->roundedDose === $calcBolus->bolus->maximum_dose)
                    <small><i>MAX</i></small>
                @elseif($calcBolus->roundedDose === $calcBolus->bolus->minimum_dose)
                    <small><i>MIN</i></small>
                @endif
            </td>
        </tr>
        <tr @class(['striped' => $loop->index % 2])>
            <td></td>
            @if(!empty($calcBolus->bolus->instructions))
                <td><u>Instructions:</u></td>
                <td colspan="5">{!! nl2br($calcBolus->bolus->instructions) !!}</td>
            @else
                <td colspan="6"></td>
            @endisset
        </tr>
    @endforeach

    <tr class="table-subsection">
        <td colspan="8"><i>CONVULSIONS</i></td>
    </tr>
    @foreach($calcBoluses->where('bolus.type', 4)->sortBy('bolus.name') as $calcBolus)
        <tr @class(['striped' => $loop->index % 2])>
            <td class="checkbox" rowspan="2"></td>
            <td>{{$calcBolus->bolus->name}}</td>
            <td class="text-right"><i>{{$calcBolus->bolus->brand_name}}</i></td>
            @if($calcBolus->bolus->commercial_concentration > 0)
                <td><strong>{{$calcBolus->bolus->commercial_concentration}} {{ $calcBolus->bolus->unit }}/mL</strong>
                </td>
            @else
                <td>-</td>
            @endif
            <td>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right"><strong>{{$calcBolus->roundedDoseString}}</strong></td>
            <td class="text-right"><strong>{{$calcBolus->roundedVolumeString}}</strong></td>
            <td>
                @if($calcBolus->roundedDose === $calcBolus->bolus->maximum_dose)
                    <small><i>MAX</i></small>
                @elseif($calcBolus->roundedDose === $calcBolus->bolus->minimum_dose)
                    <small><i>MIN</i></small>
                @endif
            </td>
        </tr>
        <tr @class(['striped' => $loop->index % 2])>
            <td></td>
            @if(!empty($calcBolus->bolus->instructions))
                <td><u>Instructions:</u></td>
                <td colspan="5">{!! nl2br($calcBolus->bolus->instructions) !!}</td>
            @else
                <td colspan="6"></td>
            @endisset
        </tr>
    @endforeach

    <tr class="table-subsection">
        <td colspan="8"><i>ANAPHYLAXIE</i></td>
    </tr>
    @foreach($calcBoluses->where('bolus.type', 5)->sortBy('bolus.name') as $calcBolus)
        <tr @class(['striped' => $loop->index % 2])>
            <td class="checkbox" rowspan="2"></td>
            <td>{{$calcBolus->bolus->name}}</td>
            <td class="text-right"><i>{{$calcBolus->bolus->brand_name}}</i></td>
            @if($calcBolus->bolus->commercial_concentration > 0)
                <td><strong>{{$calcBolus->bolus->commercial_concentration}} {{ $calcBolus->bolus->unit }}/mL</strong>
                </td>
            @else
                <td>-</td>
            @endif
            <td>{{$calcBolus->bolus->dosage}} {{ $calcBolus->bolus->unit }}/kg</td>
            <td class="text-right"><strong>{{$calcBolus->roundedDoseString}}</strong></td>
            <td class="text-right"><strong>{{$calcBolus->roundedVolumeString}}</strong></td>
            <td>
                @if($calcBolus->roundedDose === $calcBolus->bolus->maximum_dose)
                    <small><i>MAX</i></small>
                @elseif($calcBolus->roundedDose === $calcBolus->bolus->minimum_dose)
                    <small><i>MIN</i></small>
                @endif
            </td>
        </tr>
        <tr @class(['striped' => $loop->index % 2])>
            <td></td>
            @if(!empty($calcBolus->bolus->instructions))
                <td><u>Instructions:</u></td>
                <td colspan="5">{{ $calcBolus->bolus->instructions }}</td>
            @else
                <td colspan="6"></td>
            @endisset
        </tr>
    @endforeach
    </tbody>
</table>
