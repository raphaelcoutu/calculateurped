<div class="header">
    <div class="floating-left">
        <img class="logo" src="{{public_path('/img/logo-ciusss-trans.png')}}" alt="Logo CIUSSSE-CHUS">
    </div>
    <div class="floating-center">
        <h2>{{ config('app.name') }}</h2>
        <p><strong>Patient:</strong> {{ session('app.name') }} <strong>Dossier:</strong> #{{ session('app.id') }}</p>
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
                <p>(Poids <u>réel</u>: {{ session('app.weight') }} kg)</p>
            @endif
        </div>
        @else
        <div class="right-top">
            Poids @if(session('app.isWeightEstimated'))<u>estimé</u>@else<u>réel</u>@endif:
            <p class="weight">{{ session('app.dosingWeight') }} kg</p>
        </div>
        <div class="right-bottom"></div>
        @endif
    </div>
    <div class="after-box"></div>
</div>
