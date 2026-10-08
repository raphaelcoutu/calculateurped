@extends('layouts.app')

@section('content')
    <main class="mx-auto max-w-lg rounded bg-white p-6 text-gray-900 shadow dark:bg-gray-800 dark:text-gray-100">
        <h1 class="mb-5 text-2xl font-semibold">Connexion</h1>
        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1 block">Adresse courriel</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded border p-2">
                @error('email') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="mb-1 block">Mot de passe</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full rounded border p-2">
                @error('password') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2"><input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
            <button type="submit" class="rounded bg-blue-700 px-4 py-2 text-white">Se connecter</button>
        </form>
        <a class="mt-4 inline-block text-blue-700 underline" href="{{ route('password.request') }}">Mot de passe oublié?</a>
    </main>
@endsection
