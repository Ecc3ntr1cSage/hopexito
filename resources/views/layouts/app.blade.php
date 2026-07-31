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
    {{-- Swiper --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    {{-- Filepond --}}
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css"
        rel="stylesheet">
    {{-- Animate CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Scripts -->
    <script src="https://cdn.lordicon.com/xdjxvujz.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@marcreichel/alpine-typewriter@latest/dist/alpine-typewriter.min.js" defer>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

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

        /* Filepond Customization */
        .filepond--panel-root {
            background-color: #262626;
        }

        .filepond--drop-label {
            color: white;
        }

        .filepond--label-action {
            color: #6366f1;
            text-decoration: none;
        }

        .filepond--item-panel {
            background-color: #6366f1;
        }

        [data-filepond-item-state='processing-complete'] .filepond--item-panel {
            background-color: #22c55e;
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
    {{-- Global Nav Menu --}}
    @livewire('navigation-menu')
</head>

@php($isStudio = request()->routeIs('product.create'))
<body
    class="overflow-x-hidden text-xs antialiased leading-6 text-gray-200 select-none sm:text-sm font-poppins bg-zinc-900 border-box {{ $isStudio ? 'studio-page' : '' }}">
    {{ $slot }}
    @unless(request()->routeIs('login'))
        <x-auth-modal :initial-mode="request()->query('auth', 'login')" />
    @endunless
    @stack('modals')
    @livewireScripts
</body>
{{-- Global Footer --}}
@unless($isStudio)
    <x-jet-footer></x-jet-footer>
@endunless
</html>
