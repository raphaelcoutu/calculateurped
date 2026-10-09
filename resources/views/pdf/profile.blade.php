@include('pdf.header')
<h2 style="margin-top: 10px">{{ $profileName }}</h2>
<p class="note">PRÉVISUALISATION : données fictives, ne pas utiliser comme ordonnance.</p>
@foreach($profileSections as $section)
    <table class="table mt-10" style="width: 100%; word-wrap: break-word">
        <thead><tr><th style="width: 25px">MD</th><th colspan="5">{{ $section['name'] }}</th></tr>
        <tr><th></th><th>Médicament</th><th>Concentration</th><th>Posologie</th><th>Dose / volume total</th><th>Volume / débit</th></tr></thead>
        @foreach($section['items'] as $item)
            <tbody style="page-break-inside: avoid">
            @if($item['calculated'] === null)
                <tr><td></td><td>{{ $item['name'] }}</td><td colspan="4">Recette supprimée ou aucune préparation compatible avec ce poids.</td></tr>
            @elseif($item['type'] === 'bolus')
                @php($bolus = $item['calculated'])
                <tr @class(['striped' => $loop->index % 2])>
                    <td class="checkbox" rowspan="2"></td>
                    <td>{{ $bolus->bolus->name }}@if($bolus->bolus->asterisk)<sup>**</sup>@endif<br><i>{{ $bolus->bolus->brand_name }}</i></td>
                    <td>{{ $bolus->bolus->commercial_concentration }} {{ $bolus->bolus->unit }}/mL</td>
                    <td>{{ $bolus->bolus->dosage }} {{ $bolus->bolus->unit }}/kg</td>
                    <td><strong>{{ $bolus->roundedDoseString }}</strong>
                        @if($bolus->bolus->maximum_dose > 0 && $bolus->roundedDose === $bolus->bolus->maximum_dose)<small>MAX</small>
                        @elseif($bolus->bolus->minimum_dose > 0 && $bolus->roundedDose === $bolus->bolus->minimum_dose)<small>MIN</small>@endif
                    </td>
                    <td><strong>{{ $bolus->roundedVolumeString }}</strong></td>
                </tr>
                <tr @class(['striped' => $loop->index % 2])><td colspan="5">{!! nl2br(e($bolus->bolus->instructions)) !!}</td></tr>
                @if($bolus->bolus->asterisk)
                    <tr><td></td><td colspan="5">@include('pdf.bolus-dilution-note', ['weight' => $patient['weight']])</td></tr>
                @endif
            @else
                @php($infusion = $item['calculated'])
                <tr @class(['striped' => $loop->index % 2])>
                    <td class="checkbox" rowspan="2"></td>
                    <td>{{ $infusion->drug->name }}<br><i>{{ $infusion->drug->brand_name }}</i></td>
                    <td>{{ $infusion->recipe->concentration }} {{ $infusion->recipe->concentration_unit }}/mL</td>
                    <td>{{ $infusion->dosageString }}</td>
                    <td>{{ $infusion->recipe->total_volume }} mL</td>
                    <td><strong>{{ $infusion->rateString }}</strong>@if($infusion->isRateMinLimited || $infusion->isRateMaxLimited)<small>MAX</small>@endif</td>
                </tr>
                <tr @class(['striped' => $loop->index % 2])><td colspan="5">{!! nl2br(e($infusion->recipe->instructions)) !!}</td></tr>
            @endif
            </tbody>
        @endforeach
    </table>
@endforeach
