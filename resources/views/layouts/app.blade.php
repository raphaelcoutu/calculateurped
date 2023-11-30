<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
    <div id="app">
        <div class="lg:w-1/2 mx-auto p-1">
            @yield('content')
            <div class="text-sm mx-auto mt-5 border lg:w-1/3 text-center text-gray-500">
                Version: {{ config('version') }} | PHP {{ PHP_VERSION }} | Laravel v{{\Illuminate\Foundation\Application::VERSION}}
            </div>
        </div>
    </div>
</body>
</html>
