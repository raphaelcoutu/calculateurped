<div class="header">
    <div class="floating-left">
        <img class="logo" src="{{ url('/img/logo-ciusss-trans.png') }}">
        <h2 class="page-heading">{{ $pageHeading }}</h2>
        <p class="page-subheading"><i>{{ $pageSubheading ?? '' }}</i></p>
    </div>
    <div class="floating-center">
        <h2>MÉDICAMENTS URGENCES / SOINS INTENSIFS PÉDIATRIQUES</h2>
        <p><strong>Patient:</strong> {{ session('app.name') }}</p>
    </div>
    <div class="floating-right">
        <div class="right-top">
            DOSSIER:<br>{{ session('app.id') }}
        </div>
        <div class="right-bottom">
            Poids:
            <p class="weight">{{ session('app.weight') }} kg</p>
        </div>
    </div>
    <div class="after-box"></div>
</div>
