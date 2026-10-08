<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-gray-900">
    <div id="app">
        <div class="lg:w-1/2 mx-auto p-1">
            <nav class="flex justify-end items-center gap-3 py-2 text-sm">
                @auth
                    @if (auth()->user()->isSuperuser())
                        <a class="text-blue-700 underline" href="{{ route('admin.organizations.index') }}">Administration</a>
                    @else
                        <a class="text-blue-700 underline" href="{{ route('organization.profile.edit') }}">Mon centre</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-blue-700 underline" type="submit">Déconnexion</button>
                    </form>
                @else
                    <a class="text-blue-700 underline" href="{{ route('login') }}">Connexion</a>
                @endauth
            </nav>
            @if (session('status'))
                <p class="mb-3 rounded bg-green-50 p-3 text-green-800 dark:bg-green-950 dark:text-green-100" role="status">{{ session('status') }}</p>
            @endif
            @yield('content')
            <div class="text-sm mx-auto mt-5 border dark:border-gray-700 lg:w-1/3 text-center text-gray-500">
                Version: {{ config('version') }} | PHP {{ PHP_VERSION }} | Laravel v{{\Illuminate\Foundation\Application::VERSION}}
            </div>
        </div>
    </div>
</body>
</html>
