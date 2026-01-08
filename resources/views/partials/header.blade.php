<style>
    {!! file_get_contents(resource_path('views/partials/partialscss.css')) !!}
</style>
<header>
    <div class="top-bar-box">
        <div class="top-bar">
            <span class="name-topbar" aria-label="StreamBolt TV">
                <svg class="icon-play" aria-hidden="true" focusable="false" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" fill=red />
                    <polygon points="10,8 16,12 10,16" fill="currentColor"/>
                </svg>
                <span class="title">StreamBoltTV</span>
            </span>
            <div class="top-bar-spacer"></div>
            @auth
                <span class="iconletter" aria-hidden="true">
                    {{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                </span>
                <span class="user-name">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit">Terminar Sessão</button>
                </form>
            @endauth
        </div>
    </div>
</header>
