@extends('layouts.app')

@section('content')

    <h2 class="text-center mt-3">Calculateur de doses pour les urgences et les soins intensifs pédiatriques</h2>

    <form action="/" method="post" autocomplete="off">
        {{ csrf_field() }}
        <div class="row">

            <div class="form-group col-6 offset-md-2">
                <label>Nom, Prénom:</label>
                <input type="text" name="name" class="form-control" value="{{ session('form.name') }}">
            </div>

            <div class="form-group col-">
                <label>Numéro dossier:</label>
                <input type="text" name="id" class="form-control" value="{{ session('form.id') }}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-12 col-md-8 offset-md-2">
                <selector class="weight-selector"></selector>
            </div>
        </div>
        <blockquote><p>ATTENTION: il est <u><strong>impératif</strong></u> que la concentration commerciale du médicament utilisée soit la même que celle inscrite sur le calculateur de dose pour que la conversion de la dose de <strong>mg</strong> à <strong>mL</strong> soit exacte. Il est possible d’utiliser le calculateur pour prescrire la médication en mg si vous n’utilisez pas la même concentration commerciale pour un médicament donné. Il faudra seulement ajuster le volume du médicament à administrer.</p></blockquote>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger">
                    <p>Ceci est un outil d'aide à la décision, il ne s'agit pas d'une prescription pharmaceutique. Le jugement de l'équipe médicale doit s'appliquer en tout temps. Les doses suggérées ne s'appliquent pas à la population néonatale. Les auteurs ne sont pas responsables de l'usage du calculateur fait par de tiers partis.</p>
                    <p>Les doses et les volumes ont été arrondis pour faciliter l'administration des médicaments.</p>
                </div>
            </div>
        </div>
    </form>


@endsection
