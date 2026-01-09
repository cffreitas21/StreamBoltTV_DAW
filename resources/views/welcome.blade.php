<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ config('app.name', 'StreamBolt TV') }}</title>
    <style>
        /* Very small, simple styling */
        body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial; margin:0; color:#111; background:#fff; }
        .wrap { max-width:1000px; margin:0 auto; padding:16px; }
        .top-bar { display:flex; align-items:center; gap:12px; padding:8px 0; border-bottom:1px solid #eee; }
        .brand { display:flex; align-items:center; gap:8px; font-weight:700; }
        .spacer { flex:1 }
        .avatar { width:34px; height:34px; border-radius:50%; background:#222; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:700 }
        .btn { border:1px solid #ddd; background:#fafafa; padding:6px 10px; border-radius:6px; text-decoration:none; color:inherit }
        .controls { display:flex; gap:8px; align-items:center }
        .search { padding:6px 8px; border:1px solid #ddd; border-radius:6px }
        main { padding:18px 0 }
        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:12px }
        .card { border:1px solid #eee; padding:10px; border-radius:8px; display:flex; gap:10px; align-items:flex-start; background:#fff }
        .poster { width:80px; height:120px; background:#eee; display:block; border-radius:6px; object-fit:cover }
        .meta h3 { margin:0 0 6px; font-size:1rem }
        .meta p { margin:4px 0; font-size:.9rem; color:#555 }
        footer { border-top:1px solid #eee; padding:12px 0; margin-top:18px; font-size:.9rem; color:#666 }
        .visually-hidden { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0 0 0 0); white-space:nowrap; border:0 }
        .search-bar-container { display:flex; justify-content:space-between; align-items:center; margin:14px 0; }
        .welcome-title { margin:0 0 12px }
        .footer-content { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px }
        .footer-brand { font-weight:700 }
        .footer-credits { font-size:.9rem; color:#666 }
        .footer-contacts { text-align:right }
        .footer-copyright { margin-top:10px; color:#999 }
    </style>
</head>
<body>
<div class="wrap">
    <header class="top-bar" role="banner" aria-label="Main navigation bar">
        <h1 class="visually-hidden">StreamBolt TV Main Navigation</h1>

        <div class="brand" aria-label="StreamBolt TV">
            <!-- simple play icon -->
            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5V7.5l6 4-6 5z"/>
            </svg>
            StreamBolt TV
        </div>

        <div class="spacer"></div>

        <div class="controls" role="navigation" aria-label="User controls">
            <div class="avatar" aria-hidden="true">{{ strtoupper(substr($username ?? Auth::user()->name ?? 'G', 0, 1)) }}</div>
            <div class="user-name">{{ $username ?? Auth::user()->name ?? 'Guest' }}</div>
            <a href="/loginstreamer" class="btn" role="button">Terminar Sessão</a>
        </div>
    </header>

    <div class="search-bar-container">
        <div></div>
        <div>
            <label for="search" class="visually-hidden">Pesquisar filmes</label>
            <input id="search" class="search" type="search" placeholder="Search movies..." />
            <button class="btn" type="button">Search</button>
        </div>
    </div>

    <main role="main">
        <h2 class="welcome-title">Filmes Recomendados</h2>

        <div class="grid" aria-live="polite">
            <article class="card" role="button" tabindex="0" aria-label="Ver detalhes de Example Movie">
                <img class="poster" src="https://via.placeholder.com/80x120?text=Poster" alt="Example Movie poster" />
                <div class="meta">
                    <h3>Example Movie</h3>
                    <p><strong>Data:</strong> 2024-01-01</p>
                    <p><strong>Língua:</strong> English</p>
                    <p><strong>Categoria:</strong> Action, Adventure</p>
                    <p><strong>Rating:</strong> ★★★☆☆ (6/10)</p>
                </div>
            </article>

            <article class="card" role="button" tabindex="0" aria-label="Ver detalhes de Example Movie 2">
                <div class="poster" aria-hidden="true">No Image</div>
                <div class="meta">
                    <h3>Example Movie 2</h3>
                    <p><strong>Data:</strong> 2023-06-15</p>
                    <p><strong>Língua:</strong> Portuguese</p>
                    <p><strong>Categoria:</strong> Drama</p>
                    <p><strong>Rating:</strong> ★★☆☆☆ (5/10)</p>
                </div>
            </article>
        </div>
    </main>

    <footer role="contentinfo" aria-label="Footer information">
        <div class="footer-content">
            <div>
                <div class="footer-brand">StreamBolt TV</div>
                <div class="footer-credits">by Carlos Freitas &amp; Ângelo Teresa</div>
            </div>
            <div class="footer-contacts">
                <div>Contacts:</div>
                <div><a href="mailto:25959@stu.ipbeja.pt">25959@stu.ipbeja.pt</a></div>
                <div><a href="mailto:25441@stu.ipbeja.pt">25441@stu.ipbeja.pt</a></div>
            </div>
        </div>
        <div class="footer-copyright">&copy; {{ now()->year }} StreamBolt TV. All rights reserved.</div>
    </footer>
</div>
</body>
</html>
