
<h2 class="text-center mt-3">Calculateur de doses pour les urgences et les soins intensifs pédiatriques</h2>
<div class="d-flex justify-content-between">

</div>

<div class="d-flex justify-content-between mt-2">
    <div class="d-flex justify-content-between w-75">
        <div class="d-flex justify-content-between w-100">
            <h4>
                <strong>Patient</strong>: {{ session('app.name') }}  (# {{ session('app.id') }})
            </h4>
            <div>
                <h4 class="m-0">
                    <strong>Poids</strong>: {{ session('app.dosingWeight') }} kg<br>
                </h4>
                @if(session('app.weight') !== session('app.dosingWeight'))
                    <small>Poids réel: {{ session('app.weight') }} kg</small>
                @endif
            </div>
        </div>
    </div>
    <div>
        <div class="btn-group align-items-center">
            <a href="{{ url('/bolus') }}" class="btn btn-outline-primary {{ $page == 'bolus' ? 'active':''  }}">Bolus</a>
            <a href="{{ url('/perfusion') }}" class="btn btn-outline-primary {{ $page == 'infusion' ? 'active':''  }}">Perfusions</a>
            <a href="{{ url('/pdf') }}" class="btn btn-warning">PDF</a>
        </div>
    </div>
</div>
<a href="/" class="btn btn-sm btn-outline-primary align-self-end">Retour</a>