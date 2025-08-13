<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}{{ $title ?? '' }}</title>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-LXD9J0TVJ4"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-LXD9J0TVJ4');
        </script>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-227254351-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-227254351-1');
        </script>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        @stack('before-styles')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('fonts/fontawesome/css/all.min.css') }}">
        @stack('styles')

        <!-- Header Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        @stack('header-scripts')
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-SZTH3B3C45">
        </script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-SZTH3B3C45');
        </script>
    </head>
    <body class="gradient">
        <div class="font-sans text-gray-900 antialiased overflow-hidden">
        	<main class="min-h-screen">
            	{{ $slot }}
            </main>
        </div>
        <a href="https://api.whatsapp.com/send?phone=%2B40727545441" class="fixed w-16 h-16 left-10 bottom-10 bg-green-500 rounded-full text-center text-white text-3xl shadow z-10">
            <i class="fab fa-whatsapp transform translate-y-1/2"></i>
        </a>
        <!-- Footer Scripts -->
        @stack('scripts')
    </body>
</html>