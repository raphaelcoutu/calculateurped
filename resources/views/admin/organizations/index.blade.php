@extends('layouts.app')

@section('content')
    <main>
        <div class="mb-5 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Centres hospitaliers</h1>
            <a class="rounded bg-blue-700 px-4 py-2 text-white" href="{{ route('admin.organizations.create') }}">Créer un centre</a>
        </div>
        <div class="space-y-3">
            @forelse ($organizations as $organization)
                <a class="block rounded border bg-white p-4 text-gray-900 shadow-sm dark:bg-gray-800 dark:text-gray-100" href="{{ route('admin.organizations.show', $organization) }}">
                    <span class="font-medium">{{ $organization->name }}</span>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ $organization->users_count }} administrateur(s)</span>
                </a>
            @empty
                <p>Aucun centre n’a été créé.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $organizations->links() }}</div>
    </main>
@endsection
