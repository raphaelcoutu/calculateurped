@extends('layouts.app')

@section('content')
    <main class="rounded bg-white p-6 text-gray-900 shadow dark:bg-gray-800 dark:text-gray-100">
        <h1 class="mb-5 text-2xl font-semibold">Créer un centre hospitalier</h1>
        @include('admin.organizations._form', ['organization' => null, 'action' => route('admin.organizations.store'), 'method' => 'POST'])
    </main>
@endsection
