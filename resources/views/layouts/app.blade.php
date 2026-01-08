<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Projeto')</title>
    <link rel="stylesheet" href="{{ file_exists(public_path('mix-manifest.json')) ? mix('css/app.css') : asset('css/app.css') }}">
    <style>
        /* Ensure full-height and make layout a column so footer stays at bottom */
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            background-color: cornflowerblue;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Let the main content expand to push footer down */
        main {
            flex: 1;
        }
    </style>
</head>
<body>

@include('partials.header')

<main>
    @yield('content')
</main>

@include('partials.footer')
</body>
</html>
