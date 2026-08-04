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
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-6PQ7GHF3Y3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-6PQ7GHF3Y3');
</script>
    <link rel="shortcut icon" href="{{ asset('image/xito-icon.png') }}">
    <!-- Scripts -->

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

        /* html range customization */
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 18px;
            width: 18px;
            border-radius: 25%;
            background: #fff;
        }
    </style>
    @livewireStyles
</head>

@php($isStudio = request()->routeIs('product.create'))
<body
    class="site-body overflow-x-hidden text-xs antialiased leading-6 text-gray-200 select-none sm:text-sm font-poppins bg-zinc-900 border-box {{ $isStudio ? 'studio-page' : '' }}">
    {{-- Global Nav Menu --}}
    @livewire('navigation-menu')
    {{ $slot }}
    @unless(request()->routeIs('login'))
        <x-auth-modal />
    @endunless
    @stack('modals')
    @livewireScriptConfig
    {{-- Global Footer --}}
    @unless($isStudio)
        <x-jet-footer></x-jet-footer>
    @endunless
</body>
</html>
