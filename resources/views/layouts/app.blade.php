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

@auth
@if(auth()->user()->isStreamer())
<script>
    // Track time spent on page
    let pageStartTime = Date.now();
    
    function sendTimeData() {
        const duration = Math.floor((Date.now() - pageStartTime) / 1000); // duration in seconds
        
        if (duration > 0) {
            fetch('{{ route("track.time") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ duration: duration }),
                keepalive: true
            }).then(response => {
                console.log('Time tracked:', duration, 'seconds');
            }).catch(error => {
                console.error('Error tracking time:', error);
            });
        }
    }
    
    // Send time data when user leaves the page
    window.addEventListener('beforeunload', sendTimeData);
    
    // Send when page visibility changes (user switches tabs)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            sendTimeData();
            pageStartTime = Date.now();
        }
    });
    
    // Also send periodically (every 30 seconds) for long sessions
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            sendTimeData();
            pageStartTime = Date.now(); // Reset the timer
        }
    }, 30000);
</script>
@endif
@endauth
</body>
</html>
