@extends('layouts.app')

@section('content')

    <h1 class="text-center mt-3 text-2xl font-medium">
        Calculateur de doses pour les urgences et les soins intensifs pédiatriques
    </h1>

    <form action="/" method="post" autocomplete="off">
        <div>
            <div>
                <input-label>Nom, Prénom</input-label>
                <input type="text" name="name" class="form-control" value="{{ session('form.name') }}">
            </div>

            <div class="form-group col-">
                <input-label>Numéro dossier:</input-label>
                <input type="text" name="id" class="form-control" value="{{ session('form.id') }}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-12 col-md-8 offset-md-2">
                {{--                <selector class="weight-selector"></selector>--}}
                SELECTOR
            </div>
        </div>
        <blockquote class="mx-auto border-l-8 border-red-500 rounded p-2 bg-gray-100/60">
            ATTENTION: il est <u><strong>impératif</strong></u> que la concentration commerciale du
            médicament utilisée soit la même que celle inscrite sur le calculateur de dose pour que la conversion de
            la dose de <strong>mg</strong> à <strong>mL</strong> soit exacte. Il est possible d’utiliser le
            calculateur pour prescrire la médication en mg si vous n’utilisez pas la même concentration commerciale
            pour un médicament donné. Il faudra seulement ajuster le volume du médicament à administrer.
        </blockquote>
        <div class="mx-auto mt-2 bg-red-300 border rounded border-red-400 p-2 text-red-950">
            <p>Ceci est un outil d'aide à la décision, il ne s'agit pas d'une prescription pharmaceutique. Le
                jugement de l'équipe médicale doit s'appliquer en tout temps. Les doses suggérées ne
                s'appliquent pas à la population néonatale. Les auteurs ne sont pas responsables de l'usage du
                calculateur fait par de tiers partis.</p>
            <p>Les doses et les volumes ont été arrondis pour faciliter l'administration des médicaments.</p>
        </div>
    </form>

@endsection
