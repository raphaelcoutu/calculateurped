<div class="header">
    <div class="floating-left">
        <img class="logo" src="{{ url('/img/logo-ciusss-trans.png') }}">
        <h2 class="page-heading">{{ $pageHeading }}</h2>
        <p class="page-subheading"><i>{{ $pageSubheading ?? '' }}</i></p>
    </div>
    <div class="floating-center">
        <h2>MÉDICAMENTS URGENCES / SOINS INTENSIFS PÉDIATRIQUES</h2>
        <p style="padding-left:50px"><strong>Patient:</strong> {{ session('app.name') }}</p>
        <p style="padding-left:50px"><strong>Dossier: #{{ session('app.id') }}</strong></p>
    </div>
    <div class="floating-right">
        <div class="right-top">
            Poids:
            <p class="weight">{{ session('app.dosingWeight') }} kg</p>
        </div>
        <div class="right-bottom">
            @if(session('app.weight') !== session('app.dosingWeight'))
            (Poids réel: {{ session('app.weight') }} kg)
            @endif
        </div>
    </div>
    <div class="after-box"></div>
</div>
