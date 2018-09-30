
<div class="d-flex justify-content-between">
    <h2>Médicaments urgences / soins intensifs pédiatriques</h2>
    <a href="/" class="btn btn-xs btn-outline-primary align-self-end">Retour</a>
</div>

<div class="d-flex justify-content-between mt-2">
    <div class="d-flex justify-content-between w-75">
        <div class="d-flex flex-column">
            <p>
                <strong>Patient</strong>: {{ session('app.name') }}  (# {{ session('app.id') }})
            </p>
            <p>
                <strong>Âge</strong>: {{ session('app.age') }}
            </p>
        </div>
    </div>
    <div>
        <div class="btn-group align-items-center">
            <a href="{{ url('/bolus') }}" class="btn btn-outline-primary {{ $page == 'bolus' ? 'active':''  }}">Bolus</a>
            <a href="{{ url('/perfusion') }}" class="btn btn-outline-primary {{ $page == 'infusion' ? 'active':''  }}">Perfusions</a>
            <a href="{{ url('/pdf') }}" class="btn btn-warning">PDF</a>
        </div>
        <h2 class="p-2"><strong>Poids</strong>: {{ session('app.weight') }} kg</h2>
    </div>
</div>