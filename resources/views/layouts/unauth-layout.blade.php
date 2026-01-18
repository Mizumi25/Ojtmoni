<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'OJT Track') }}</title>

        <!-- Fonts -->
        <!-- <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->

          <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireScripts
    @livewireStyles
        
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center sm:pt-0 bg-white">
            <div class="hidden sm:block">
                <livewire:nav.header />
            </div>

            <div class="py-4 bg-gray-100 shadow-md overflow-hidden sm:rounded-lg flex items-center justify-center min-h-[100vh] w-full relative">
                <div class="absolute w-[300px] h-[300px] sm:w-[700px] sm:h-[700px] md:w-[800px] md:h-[800px] lg:w-[1000px] lg:h-[1000px] rounded-full bg-emerald-100 blur-3xl opacity-60 z-0"></div>
                @yield('content')
            </div>
        </div>
    </body>
</html>
