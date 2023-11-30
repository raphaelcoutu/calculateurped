@extends('layouts.app')

@section('content')

    <h1 class="text-center mt-3 text-2xl font-medium">
        Calculateur de doses pour les urgences et les soins intensifs pédiatriques
    </h1>

    <form action="/" method="post" autocomplete="off">
        @csrf
        <div class="flex justify-center">
            <div class="flex lg:w-2/3 w-full">
                <div class="w-full">
                    <input-label>Nom, Prénom</input-label>
                    <text-input name="name" value="{{ session('form.name') }}"/>
                </div>

                <div class="w-full ml-2">
                    <input-label>Numéro dossier:</input-label>
                    <text-input name="id" value="{{ session('form.id') }}"/>
                </div>
            </div>
        </div>
        <selector></selector>
    </form>
    <blockquote class="mt-10 mx-auto border-l-8 border-red-500 rounded p-2 bg-red-50 shadow">
        ATTENTION: il est <u><strong>impératif</strong></u> que la concentration commerciale du
        médicament utilisée soit la même que celle inscrite sur le calculateur de dose pour que la conversion de
        la dose de <strong>mg</strong> à <strong>mL</strong> soit exacte. Il est possible d’utiliser le
        calculateur pour prescrire la médication en mg si vous n’utilisez pas la même concentration commerciale
        pour un médicament donné. Il faudra seulement ajuster le volume du médicament à administrer.
    </blockquote>
    <div class="mx-auto mt-2 border-l-8 border-amber-300 bg-white rounded p-2 bg-yellow-50 text-yellow-950 shadow">
        <p>Ceci est un outil d'aide à la décision, il ne s'agit pas d'une prescription pharmaceutique. Le
            jugement de l'équipe médicale doit s'appliquer en tout temps. Les doses suggérées ne
            s'appliquent pas à la population néonatale. Les auteurs ne sont pas responsables de l'usage du
            calculateur fait par de tiers partis.</p>
        <p>Les doses et les volumes ont été arrondis pour faciliter l'administration des médicaments.</p>
    </div>

@endsection
