@extends('layouts.app')

@section('content')

    <h1 class="text-center">Médicaments pour transport</h1>

    <form action="/" method="post">
        {{ csrf_field() }}
        <div class="row">

            <div class="form-group col-xs-12 col-md-6 col-md-offset-3">
                <label>Nom, Prénom:</label>
                <input type="text" name="name" class="form-control" value="{{ session('form.name') }}">
            </div>


            <div class="form-group col-xs-6 col-md-3 col-md-offset-3">
                <label>Âge:</label>
                <input type="text" name="age" class="form-control" value="{{ session('form.age') }}">
            </div>

            <div class="form-group col-xs-6 col-md-3">
                <label>Numéro dossier:</label>
                <input type="text" name="id" class="form-control" value="{{ session('form.id') }}">
            </div>

            <div class="form-group col-xs-12 col-md-6 col-md-offset-3">
                <label>Poids:</label> <i>(en kg)</i>
                <input type="text" name="weight" class="form-control" value="{{ session('form.weight') }}">
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


    </form>


@endsection