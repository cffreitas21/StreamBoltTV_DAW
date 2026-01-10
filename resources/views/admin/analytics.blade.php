{{-- Vista: Estatísticas - Dashboard com métricas de utilizadores streamers --}}
@extends('layouts.app')

@section('content')
    <style>
        {!! file_get_contents(resource_path('views/admin/admin.css')) !!}
    </style>

    <div class="top-bar-box">
        <div class="top-bar">
            <div class="name-topbar">
                <button type="submit" class="nb-submit"
                        onclick="window.location.href='{{ url('/addmovie') }}'">
                    Adicionar Filme</button>
                <button type="submit" class="nb-submit"
                        onclick="window.location.href='{{ url('/homepageadm') }}'">
                    Aprovação de Comentários</button>
                <button type="submit" class="nb-submit"
                        onclick="window.location.href='{{ url('/analytics') }}'">
                    Estatísticas</button>
            </div>

            <div class="top-bar-spacer"></div>

            {{-- Campo de pesquisa com autocomplete --}}
            <div class="search-container">
                <input
                    id="search-input"
                    type="text"
                    placeholder="Pesquisar Filmes..."
                    aria-label="Pesquisar Filmes"
                    autocomplete="off"
                />
                <div id="search-dropdown" class="search-dropdown"></div>
            </div>
        </div>
    </div>
   
    <div class="center-title">
        <h1>Estatísticas dos Streamers</h1>
    </div>
    
    <div class="analytics-container">
        <div class="table-section">
            <table class="analytics-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Total de Acessos</th>
                        <th>Tempo Total na Plataforma</th>
                        <th>Tempo Médio por Sessão</th>
                        <th>Pesquisas Realizadas</th>
                        <th>Comentários Feitos</th>
                        <th>Última Atividade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($streamers as $streamer)
                        <tr>
                            <td>{{ $streamer['name'] }}</td>
                            <td>{{ $streamer['logins'] }}</td>
                            <td>{{ gmdate('H:i:s', $streamer['total_time']) }}</td>
                            <td>{{ gmdate('H:i:s', $streamer['avg_time_per_session']) }}</td>
                            <td>{{ $streamer['searches'] }}</td>
                            <td>{{ $streamer['comments'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($streamer['last_activity'])->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="analytics-empty-state">
                                Nenhum dado de streamers disponível
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('search-input');
        const searchDropdown = document.getElementById('search-dropdown');
        let searchTimeout;

        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchDropdown.classList.remove('show');
                searchDropdown.innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/movies/search?q=${encodeURIComponent(query)}`);
                    const movies = await response.json();
                    
                    if (movies.length === 0) {
                        searchDropdown.innerHTML = '<div class="search-result-item">Nenhum filme encontrado</div>';
                        searchDropdown.classList.add('show');
                        return;
                    }
                    
                    const resultsHTML = movies.map(movie => {
                        const posterHTML = movie.poster_path 
                            ? `<img src="/storage/${movie.poster_path}" alt="${movie.title}" class="search-result-poster">`
                            : `<div class="search-result-poster">No Img</div>`;
                        
                        return `
                            <div class="search-result-item" onclick="window.location.href='/moviedetailsadm?id=${movie.id}'">
                                ${posterHTML}
                                <div class="search-result-title">${movie.title}</div>
                            </div>
                        `;
                    }).join('');
                    
                    searchDropdown.innerHTML = resultsHTML;
                    searchDropdown.classList.add('show');
                } catch (error) {
                    console.error('Search error:', error);
                }
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.remove('show');
            }
        });
    </script>
@endsection
