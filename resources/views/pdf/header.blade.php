<div class="header">
    <img class="floating-logo" src="{{ url('/img/logo-ciusss-trans.png') }}">
    <div class="floating-center">
        <h2>Médicaments urgences / soins intensifs pédiatriques</h2>
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
