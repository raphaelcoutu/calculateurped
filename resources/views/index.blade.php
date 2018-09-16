@extends('layouts.app')

@section('content')

    <h1 class="text-center">Calculateur de doses pour les urgences et les soins intensifs pédiatriques</h1>

    <form action="/" method="post">
        {{ csrf_field() }}
        <div class="row">

            <div class="form-group col-xs-12 col-md-6 col-md-offset-3">
                <label>Nom, Prénom:</label>
                <input type="text" name="name" class="form-control" value="{{ session('form.name') }}">
            </div>


            <div class="form-group col-xs-6 col-md-3 col-md-offset-3">

            </div>

            <div class="form-group col-xs-6 col-md-3">
                <label>Numéro dossier:</label>
                <input type="text" name="id" class="form-control" value="{{ session('form.id') }}">
            </div>

            <div class="form-group col-xs-12 col-md-6 col-md-offset-3">
                <weight-selector></weight-selector>
            </div>
        </div>
        <div class="row">
            <!--  Form Submit -->
            <div class="form-group col-xs-6 col-md-3 col-md-offset-3">
                <input type="submit" value="Go!" class="form-control btn btn-primary" />
            </div>
            <div class="col-xs-6 col-md-3">
                <a href="/reset" class="btn btn-danger form-control">Mise à zéro</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well alert-danger">
                    <p>Ceci est un outil d'aide à la décision, il ne s'agit pas d'une prescription pharmaceutique et que le jugement de l'équipe médicale doit s'appliquer en tout temps. Les doses suggérées ne s'appliquent pas à la population néonatale. Les auteurs ne sont pas responsables de l'usage du calculateur fait par de tiers partis.</p>
                </div>
            </div>

        </div>
    </form>


@endsection