@extends('layouts.app')

@section('content')
    <main class="mx-auto max-w-lg rounded bg-white p-6 text-gray-900 shadow dark:bg-gray-800 dark:text-gray-100">
        <h1 class="mb-5 text-2xl font-semibold">Choisir un mot de passe</h1>
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label for="email" class="mb-1 block">Adresse courriel</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="username" class="w-full rounded border p-2">
                @error('email') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="mb-1 block">Mot de passe (12 caractères minimum)</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="w-full rounded border p-2">
                @error('password') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-1 block">Confirmer le mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded border p-2">
            </div>
            <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Enregistrer le mot de passe</button>
        </form>
    </main>
@endsection
