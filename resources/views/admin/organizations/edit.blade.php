@extends('layouts.app')

@section('content')
    <main class="rounded bg-white p-6 text-gray-900 shadow dark:bg-gray-800 dark:text-gray-100">
        <h1 class="mb-5 text-2xl font-semibold">Modifier {{ $organization->name }}</h1>
        @include('admin.organizations._form', ['action' => route('admin.organizations.update', $organization), 'method' => 'PUT'])
    </main>
@endsection
