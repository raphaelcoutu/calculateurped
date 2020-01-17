@include('pdf.header', ['pageHeading' => 'Bolus'])
<table width="540" class="table-striped">
    <thead>
    <tr>
        <th width="15">MD</th>
        <th colspan="2">Urgence/réanimation</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose</th>
        <th width="100" colspan="2">Volume</th>
    </tr>
    </thead>
    <tbody>
        <tr class="bg-white border-bottom">
            <td colspan="8" class="text-center"><i>IV DIRECT</i></td>
        </tr>
    @foreach($calcBoluses->where('bolus.type', 1)->sortBy('bolus.name') as $calcBolus)
        <tr>
            <td class="checkbox"></td>
            {{-- Si on a pas de nom commercial, on fusionne les colonnes --}}
            @if(isset($calcBolus->bolus->brand_name) && !empty($calcBolus->bolus->brand_name))
                <td width="110">
                    @if($calcBolus->bolus->asterisk)
                        <span>** <sup>voir note</sup></span>
                    @endif
                    {{$calcBolus->bolus->name}}
                </td>
                <td class="text-right" width="40"><i>{{$calcBolus->bolus->brand_name}}</i></td>
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
@if(session('app.weight') <= 15)
    <p class="note">** Doit être dilué dans 50 mL et administré sur 15-30 minutes si le patient n'est pas en arrêt cardiorespiratoire.</p>
@else
    <p class="note">** Doit être dilué dans 100 mL et administré sur 15-30 minutes si le patient n'est pas en arrêt cardiorespiratoire.</p>
@endif
<table width="540" class="table-striped">
    <thead>
    <tr>
        <th width="15">MD</th>
        <th colspan="2">Intubation séquence rapide</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose</th>
        <th width="100" colspan="2">Volume</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcBoluses->where('bolus.type', 2)->sortBy('bolus.name') as $calcBolus)
        <tr>
            <td class="checkbox"></td>
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

<table width="540" class="table-striped-2">
    <thead>
    <tr>
        <th width="15">MD</th>
        <th colspan="2">Autres médicaments</th>
        <th width="50">Concentration<br>commerciale</th>
        <th width="50">Posologie</th>
        <th width="50">Dose</th>
        <th width="80" colspan="2">Volume</th>
    </tr>
    </thead>
    <tbody>
     {{--Hypertension intracranienne--}}
    <tr class="bg-white border-bottom">
        <td colspan="8" rowspan="2"><i>HYPERTENSION INTRACRANIENNE</i></td>
    </tr>
    <tr class="bg-white"></tr>
    @foreach($calcBoluses->where('bolus.type', 3)->sortBy('bolus.name') as $calcBolus)
        <tr>
            <td class="checkbox" rowspan="2"></td>
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
            @if(!empty($calcBolus->bolus->instructions))
                <td><u>Instructions:</u></td>
                <td colspan="5">{!! nl2br($calcBolus->bolus->instructions) !!}</td>
            @else
                <td colspan="6"></td>
            @endisset
        </tr>
    @endforeach

     {{--Épilepsie--}}
    <tr class="bg-white border-bottom">
        <td colspan="8" rowspan="2"><i>ÉPILEPSIE</i></td>
    </tr>
     <tr class="bg-white"></tr>
    @foreach($calcBoluses->where('bolus.type', 4)->sortBy('bolus.name') as $calcBolus)
        <tr>
            <td class="checkbox" rowspan="2"></td>
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
            @if(!empty($calcBolus->bolus->instructions))
                <td><u>Instructions:</u></td>
                <td colspan="5">{!! nl2br($calcBolus->bolus->instructions) !!}</td>
            @else
                <td colspan="6"></td>
            @endisset
        </tr>
    @endforeach

     {{--Anaphylaxie--}}
    <tr class="bg-white border-bottom">
        <td colspan="8" rowspan="2"><i>ANAPHYLAXIE</i></td>
    </tr>
    <tr class="bg-white"></tr>
    @foreach($calcBoluses->where('bolus.type', 5)->sortBy('bolus.name') as $calcBolus)
        <tr>
            <td class="checkbox" rowspan="2"></td>
            <td class="indent" width="120">{{$calcBolus->bolus->name}}</td>
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

<table width="540" class="table-striped">
    <thead>
    <tr>
        <th width="5">MD</th>
        <th width="175">Défibrillation</th>
        <th width="120">Posologie</th>
        <th width="120" colspan="2">Dose</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calcBoluses->where('bolus.type', 6) as $calcBolus)
        <tr>
            <td class="checkbox"></td>
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
