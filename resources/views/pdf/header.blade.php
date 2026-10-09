<div class="header">
    <div class="floating-left">
        <img class="logo" src="{{public_path('/img/logo-ciusss-trans.png')}}" alt="Logo CIUSSSE-CHUS">
    </div>
    <div class="floating-center">
        <h2>{{ config('app.name') }}</h2>
        <p><strong>Patient:</strong> {{ ($patient['name'] ?? session('app.name')) }} <strong>Dossier:</strong> #{{ ($patient['id'] ?? session('app.id')) }}</p>
    </div>
    <div class="floating-right">
        @if(($patient['weight'] ?? session('app.weight')) !== ($patient['dosingWeight'] ?? session('app.dosingWeight')))
        <div class="right-top">
            Poids de calcul:
            <p class="weight">{{ ($patient['dosingWeight'] ?? session('app.dosingWeight')) }} kg</p>
        </div>
        <div class="right-bottom">
            @if(($patient['isWeightEstimated'] ?? session('app.isWeightEstimated')) === "true")
                <p>(Poids <u>estimé</u>: {{ ($patient['weight'] ?? session('app.weight')) }} kg)</p>
            @else
                <p>(Poids <u>réel</u>: {{ ($patient['weight'] ?? session('app.weight')) }} kg)</p>
            @endif
        </div>
        @else
        <div class="right-top">
            Poids
            @if(($patient['isWeightEstimated'] ?? session('app.isWeightEstimated')) === "true")
                <u>estimé</u>:
            @else
                <u>réel</u>:
            @endif
            <p class="weight">{{ ($patient['dosingWeight'] ?? session('app.dosingWeight')) }} kg</p>
        </div>
        <div class="right-bottom"></div>
        @endif
    </div>
    <div class="after-box"></div>
</div>
