<div class="header">
    <div class="floating-left">
        <img class="logo" src="{{ url('/img/logo-ciusss-trans.png') }}">
        <h2 class="page-heading">{{ $pageHeading }}</h2>
        <p class="page-subheading"><i>{{ $pageSubheading ?? '' }}</i></p>
    </div>
    <div class="floating-center">
        <h2>{{ config('app.name') }}</h2>
        <p style="padding-left:50px"><strong>Patient:</strong> {{ session('app.name') }}</p>
        <p style="padding-left:50px"><strong>Dossier: #{{ session('app.id') }}</strong></p>
    </div>
    <div class="floating-right">
        @if(session('app.weight') !== session('app.dosingWeight'))
        <div class="right-top">
            Poids de calcul:
            <p class="weight">{{ session('app.dosingWeight') }} kg</p>
        </div>
        <div class="right-bottom">
            @if(session('app.isWeightEstimated'))
                <p>(Poids <u>estimé</u>: {{ session('app.weight') }} kg)</p>
            @else
                <p>(Poids <u>réél</u>: {{ session('app.weight') }} kg)</p>
            @endif
        </div>
        @else
        <div class="right-top">
            Poids @if(session('app.isWeightEstimated'))<u>estimé</u>@else<u>réél</u>@endif:
            <p class="weight">{{ session('app.dosingWeight') }} kg</p>
        </div>
        <div class="right-bottom"></div>
        @endif
    </div>
    <div class="after-box"></div>
</div>
