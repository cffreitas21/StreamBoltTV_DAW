{{-- Vista: Homepage do Streamer - Lista de filmes com pesquisa autocomplete --}}
@extends('layouts.app')

@section('content')
    <style>
        {!! file_get_contents(resource_path('views/streamer/streamer.css')) !!}
    </style>

    {{-- Barra superior com pesquisa --}}
    <div class="top-bar-box">
        <div class="top-bar">
            <div class="name-topbar"></div>
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
        <h1>Filmes Recomendados</h1>
    </div>

    {{-- Container onde os filmes são carregados dinamicamente --}}
    <div class="movies-container" id="moviesGrid">
        <div class="loading">A carregar filmes...</div>
    </div>

    <script>
        let searchTimeout;
        const searchInput = document.getElementById('search-input');
        const searchDropdown = document.getElementById('search-dropdown');

        // Funcionalidade de pesquisa com debounce de 300ms
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
                    // Chama API de pesquisa com query string
                    const response = await fetch(`/api/movies/search?q=${encodeURIComponent(query)}`);
                    const movies = await response.json();
                    
                    if (movies.length === 0) {
                        searchDropdown.innerHTML = '<div class="search-result-item">Nenhum filme encontrado</div>';
                        searchDropdown.classList.add('show');
                        return;
                    }
                    
                    // Constrói HTML dos resultados da pesquisa
                    const resultsHTML = movies.map(movie => {
                        const posterHTML = movie.poster_path 
                            ? `<img src="/storage/${movie.poster_path}" alt="${movie.title}" class="search-result-poster">`
                            : `<div class="search-result-poster">No Img</div>`;
                        
                        return `
                            <div class="search-result-item" onclick="window.location.href='/moviedetails?id=${movie.id}'">
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

        // Fecha dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.remove('show');
            }
        });

        // Carrega todos os filmes ao carregar a página
        document.addEventListener('DOMContentLoaded', async function() {
            const moviesGrid = document.getElementById('moviesGrid');
            
            try {
                // Busca lista completa de filmes da API
                const response = await fetch('/api/movies');
                
                if (!response.ok) {
                    throw new Error('Erro ao carregar filmes');
                }
                
                const movies = await response.json();
                
                if (movies.length === 0) {
                    moviesGrid.innerHTML = '<div class="no-movies">Nenhum filme disponível</div>';
                    return;
                }
                
                // Gera HTML dos cartões de filmes
                const gridHTML = movies.map(movie => {
                    const posterHTML = movie.poster_path 
                        ? `<img src="/storage/${movie.poster_path}" alt="${movie.title}">`
                        : `<div class="movie-poster-placeholder">${movie.title}</div>`;
                    
                    const year = movie.release_date ? new Date(movie.release_date).getFullYear() : 'N/A';
                    const rating = parseFloat(movie.vote_average).toFixed(1);
                    const voteAverage = parseFloat(movie.vote_average) || 0;
                    
                    // Cria 5 estrelas - preenchidas ou vazias baseado no rating (0-10 convertido para 0-5)
                    const stars = Array.from({ length: 5 }, (_, i) => 
                        voteAverage / 2 >= i + 1 ? '★' : '☆'
                    ).join('');
                    
                    return `
                        <div class="movie-card" onclick="window.location.href='/moviedetails?id=${movie.id}'">
                            <div class="movie-poster">
                                ${posterHTML}
                            </div>
                            <div class="movie-info">
                                <div class="movie-title">${movie.title}</div>
                                <div class="movie-meta">
                                    <span class="movie-rating-stars">${stars}</span>
                                    <span class="movie-rating-number">${rating}/10</span>
                                </div>
                                <div class="movie-year">${year}</div>
                                <span class="movie-genre">${movie.genre}</span>
                            </div>
                        </div>
                    `;
                }).join('');
                
                moviesGrid.innerHTML = `<div class="movies-grid">${gridHTML}</div>`;
                
            } catch (error) {
                console.error('Error:', error);
                moviesGrid.innerHTML = '<div class="error-message">Erro ao carregar filmes. Tente novamente.</div>';
            }
        });
    </script>
@endsection
