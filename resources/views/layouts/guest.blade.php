<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <x-google-analytics />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Simplifying Registration</title>
        <meta name="description" content="Director of Education, BTC provides a unified platform for Student Admissions, School & Tuition Management, and Job Applications with integrated secure payments. Simplifying Registration.">
        <meta name="keywords" content="Director of Education, BTC, Education Management, School ERP, Student Admission, Job Portal, Tuition Management, Secure Payments, Digital India, Assam Education">
        <meta name="author" content="Director of Education, BTC">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="Director of Education, BTC - Simplifying Registration">
        <meta property="og:description" content="Unified platform for Student Admissions, School & Tuition Management, and Job Applications with integrated secure payments.">
        <meta property="og:image" content="{{ asset('image/logo/logo.png') }}">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="Director of Education, BTC - Simplifying Registration">
        <meta name="twitter:description" content="Unified platform for Student Admissions, School & Tuition Management, and Job Applications with integrated secure payments.">
        <meta name="twitter:image" content="{{ asset('image/logo/logo.png') }}">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('image/logo/logo-em.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('image/logo/logo-em.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Tailwind CSS (CDN as requested) -->
        <script src="https://cdn.tailwindcss.com"></script>
        @include('layouts.partials.tailwind-config')
        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <style>
            body { font-family: 'Inter', sans-serif; }
            .glass-nav {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
            }
            .hero-pattern {
                background-color: #f8fafc;
                background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
                background-size: 24px 24px;
            }
            /* Smooth scrolling */
            html { scroll-behavior: smooth; }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased">

        <x-web-header />

        {{ $slot }}

        <x-web-footer />

        <script>
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        </script>
    </body>
</html>
