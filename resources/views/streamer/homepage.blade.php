@extends('layouts.app')

@section('content')
    <style>
        {!! file_get_contents(resource_path('views/streamer/streamer.css')) !!}
    </style>

    <div class="top-bar-box">
        <div class="top-bar">
            <div class="name-topbar"></div>
            <div class="top-bar-spacer"></div>
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

    <div class="movies-container" id="moviesGrid">
        <div class="loading">A carregar filmes...</div>
    </div>

    <script>
        let searchTimeout;
        const searchInput = document.getElementById('search-input');
        const searchDropdown = document.getElementById('search-dropdown');

        // Search functionality
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

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.remove('show');
            }
        });

        // Load movies
        document.addEventListener('DOMContentLoaded', async function() {
            const moviesGrid = document.getElementById('moviesGrid');
            
            try {
                const response = await fetch('/api/movies');
                
                if (!response.ok) {
                    throw new Error('Erro ao carregar filmes');
                }
                
                const movies = await response.json();
                
                if (movies.length === 0) {
                    moviesGrid.innerHTML = '<div class="no-movies">Nenhum filme disponível</div>';
                    return;
                }
                
                const gridHTML = movies.map(movie => {
                    const posterHTML = movie.poster_path 
                        ? `<img src="/storage/${movie.poster_path}" alt="${movie.title}">`
                        : `<div class="movie-poster-placeholder">${movie.title}</div>`;
                    
                    const year = movie.release_date ? new Date(movie.release_date).getFullYear() : 'N/A';
                    const rating = parseFloat(movie.vote_average).toFixed(1);
                    
                    return `
                        <div class="movie-card" onclick="window.location.href='/moviedetails?id=${movie.id}'">
                            <div class="movie-poster">
                                ${posterHTML}
                            </div>
                            <div class="movie-info">
                                <div class="movie-title">${movie.title}</div>
                                <div class="movie-meta">
                                    <span class="movie-rating">★ ${rating}</span>
                                    <span class="movie-year">${year}</span>
                                </div>
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
