
<h1>Médicament transport <a href="/" class="btn btn-xs btn-primary pull-right">Retour</a></h1>

<div class="row">
    <div class="col-md-6">
        <p>
            <strong>Patient</strong>: {{ session('app.name') }}  (# {{ session('app.id') }})
        </p>
        <p>
            <strong>Âge</strong>: {{ session('app.age') }}
        </p>
    </div>
    <div class="col-md-3">
        <h2><strong>Poids</strong>: {{ session('app.weight') }} kg</h2>
    </div>
    <div class="col-md-3">
        <div class="btn-group pull-right">
            <a href="{{ url('/bolus') }}" class="btn btn-default {{ $page == 'bolus' ? 'active':''  }}">Bolus</a>
            <a href="{{ url('/perfusion') }}" class="btn btn-default {{ $page == 'infusion' ? 'active':''  }}">Perfusions</a>
            <a href="{{ url('/pdf') }}" class="btn btn-warning">PDF</a>
        </div>
    </div>
</div>