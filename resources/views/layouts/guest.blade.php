<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Meu Financeiro') }}</title>


    <link rel="icon" type="image/svg+xml" href="{{ asset('brand/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('brand/favicon.ico') }}"> {{-- opcional --}}
    <link rel="apple-touch-icon" href="{{ asset('brand/apple-touch-icon.png') }}"> {{-- opcional --}}
    <meta name="theme-color" content="#4f46e5">

<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<script src="{{ asset('js/app.js') }}"></script>

</head>

<body class="min-h-screen bg-slate-100">
    {{ $slot }}
</body>

</html>