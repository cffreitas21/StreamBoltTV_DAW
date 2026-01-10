<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>StreamBoltTV</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            html, body {
                height: 100%;
            }

            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                margin: 0;
                background-color: slategray;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            
            header {
                position: sticky;
                top: 0;
                z-index: 1000;
                background-color: slategray;
            }
            
            footer {
                position: sticky;
                bottom: 0;
                z-index: 1000;
                background-color: slategray;
            }

            main {
                flex: 1;
                overflow-y: auto;
            }
        </style>
    </head>
    <body>
        @include('partials.header')
        
        <main>
            <div class="min-h-screen flex flex-col items-center" style="padding-top: 160px;">
                <div class="w-full sm:max-w-md px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        </main>
        
        @include('partials.footer')
    </body>
</html>
