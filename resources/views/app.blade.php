<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Calculateur pédiatrique') }}</title>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    <x-inertia::head />
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased dark:bg-[#101a1a] dark:text-slate-100">
    <x-inertia::app />
</body>
</html>
