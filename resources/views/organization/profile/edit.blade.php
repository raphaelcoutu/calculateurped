@extends('layouts.app')

@section('content')
    <main class="rounded bg-white p-6 text-gray-900 shadow dark:bg-gray-800 dark:text-gray-100">
        <h1 class="mb-5 text-2xl font-semibold">Identité de mon centre</h1>
        <form method="POST" action="{{ route('organization.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="mb-1 block">Nom du centre</label>
                <input id="name" name="name" value="{{ old('name', $organization->name) }}" required maxlength="255" class="w-full rounded border p-2">
                @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            @if ($organization->logo_path)
                <img src="{{ Storage::disk('public')->url($organization->logo_path) }}" alt="Logo de {{ $organization->name }}" class="mb-2 max-h-24 max-w-48 object-contain">
            @endif
            <div>
                <label for="logo" class="mb-1 block">Logo (JPG, PNG ou WebP, 2 Mo maximum)</label>
                <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp" class="w-full rounded border p-2">
                @error('logo') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Enregistrer les changements</button>
        </form>
    </main>
@endsection
