<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HopeXito')</title>
    <meta property="og:image" content="@yield('thumbnail')" />
    <!-- Fonts -->
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    <link rel="shortcut icon" href="{{ asset('image/xito-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Styles -->
    <style>
        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        /* Hide Scrollbar */
        ::-webkit-scrollbar {
            display: none;
        }

        /* Reconfigure Input Autocomplete Background Color */
        input:-webkit-autofill {
            transition: background-color 100s ease-in-out 0s;
            -webkit-text-fill-color: white !important;
        }

        /* Hide AlpineJS Component */
        [x-cloak] {
            display: none;
        }
    </style>
    @livewireStyles
</head>

<body
    class="overflow-x-hidden text-xs antialiased leading-6 text-gray-200 select-none sm:text-sm font-poppins bg-zinc-900 border-box">
    {{ $slot }}
    @stack('modals')
    @livewireScriptConfig
</body>
</html>
