@extends('layouts.app')

@section('content')
    <main>
        <div class="mb-5 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">{{ $organization->name }}</h1>
            <a class="text-blue-700 underline" href="{{ route('admin.organizations.edit', $organization) }}">Modifier le centre</a>
        </div>
        @if ($organization->logo_path)
            <img src="{{ Storage::disk('public')->url($organization->logo_path) }}" alt="Logo de {{ $organization->name }}" class="mb-5 max-h-24 max-w-48 object-contain">
        @endif
        <h2 class="mb-3 text-xl font-semibold">Administrateurs</h2>
        <ul class="mb-6 divide-y rounded border bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
            @forelse ($organization->users as $administrator)
                @unless ($administrator->isSuperuser())
                    <li class="p-3">{{ $administrator->name }} — {{ $administrator->email }}</li>
                @endunless
            @empty
                <li class="p-3">Aucun compte administrateur.</li>
            @endforelse
        </ul>
        @if (auth()->user()->isSuperuser())
            <section class="rounded bg-white p-5 text-gray-900 shadow dark:bg-gray-800 dark:text-gray-100">
                <h2 class="mb-4 text-xl font-semibold">Inviter un administrateur</h2>
                <form method="POST" action="{{ route('admin.organizations.administrators.store', $organization) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="administrator_name" class="mb-1 block">Nom</label>
                        <input id="administrator_name" name="name" value="{{ old('name') }}" required maxlength="255" class="w-full rounded border p-2">
                        @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="administrator_email" class="mb-1 block">Adresse courriel</label>
                        <input id="administrator_email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded border p-2">
                        @error('email') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Créer le compte et envoyer le lien</button>
                </form>
            </section>
        @endif
    </main>
@endsection
