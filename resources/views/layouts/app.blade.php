<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <x-google-analytics />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('image/logo/logo-em.png') }}">
        <meta name="robots" content="noindex, nofollow">
        <meta name="author" content="Director of Education, BTC">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            @if(session()->has('impersonator_id'))
                <div class="bg-indigo-600 px-4 py-3 text-white sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">
                    <p class="text-center text-sm font-medium sm:text-left">
                        You are currently impersonating <strong>{{ auth()->user()->name }}</strong>.
                    </p>
                    <form method="POST" action="{{ route('impersonate.leave') }}" class="mt-4 sm:ml-4 sm:mt-0 sm:flex-shrink-0">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            Return to Admin
                        </button>
                    </form>
                </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
