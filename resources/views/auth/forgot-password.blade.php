@extends('layouts.app')

@section('content')
    <main class="mx-auto max-w-lg rounded bg-white p-6 text-gray-900 shadow dark:bg-gray-800 dark:text-gray-100">
        <h1 class="mb-3 text-2xl font-semibold">Définir ou réinitialiser un mot de passe</h1>
        <p class="mb-5">Saisissez l’adresse courriel associée à votre compte. Nous vous enverrons un lien sécurisé.</p>
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1 block">Adresse courriel</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded border p-2">
                @error('email') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Envoyer le lien</button>
        </form>
    </main>
@endsection
