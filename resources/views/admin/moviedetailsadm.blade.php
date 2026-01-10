{{-- Vista: Detalhes do Filme (Admin) - Visualização e gestão de filme e comentários --}}
@extends('layouts.app')

@section('content')
    <style>
        {!! file_get_contents(resource_path('views/admin/admin.css')) !!}
        {!! file_get_contents(resource_path('views/streamer/streamer.css')) !!}
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

    <main class="center-content" id="mainContent">
        <div class="loading-text">A carregar...</div>
    </main>

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

        // Carrega detalhes do filme para gestão admin
        document.addEventListener('DOMContentLoaded', async function() {
            const mainContent = document.getElementById('mainContent');
            const urlParams = new URLSearchParams(window.location.search);
            const movieId = urlParams.get('id');

            if (!movieId) {
                mainContent.innerHTML = '<div class="loading-text">ID do filme não encontrado</div>';
                return;
            }

            try {
                const response = await fetch(`/api/movies/${movieId}`);
                
                if (!response.ok) {
                    throw new Error('Filme não encontrado');
                }
                
                const movie = await response.json();
                const comments = movie.comments || [];

                renderMovieDetails(movie, comments);

            } catch (error) {
                console.error('Error:', error);
                mainContent.innerHTML = '<div class="loading-text">Erro ao carregar filme: ' + error.message + '</div>';
            }
        });

        // Renderiza detalhes do filme com botão de delete
        function renderMovieDetails(movie, comments) {
            const mainContent = document.getElementById('mainContent');
            
            const posterHTML = movie.poster_path 
                ? `<img class="movie-poster" src="/storage/${movie.poster_path}" alt="${movie.title}">`
                : `<div class="no-poster">No Image</div>`;

            const voteAverage = parseFloat(movie.vote_average) || 0;
            const stars = Array.from({ length: 5 }, (_, i) => 
                voteAverage / 2 >= i + 1 ? '★' : '☆'
            ).join('');

            // Todos os comentários têm botão de delete (admin)
            const commentsHTML = comments.map(c => `
                <div class="review-card">
                    <div class="review-header">
                        <div class="icon-letra">${c.user?.name ? c.user.name.charAt(0).toUpperCase() : 'U'}</div>
                        <span class="review-author">${c.user?.name || 'Utilizador'}</span>
                        <span class="review-date">${new Date(c.created_at).toLocaleDateString('pt-PT')}</span>
                        <button class="delete-btn" onclick="deleteComment(${c.id})">Apagar Comentário</button>
                    </div>
                    <div class="review-content">${c.comment}</div>
                </div>
            `).join('');

            mainContent.innerHTML = `
                <div>
                    <div class="movie-details">
                        ${posterHTML}
                        <div class="movie-info">
                            <h1 class="movie-title">${movie.title || 'Sem título'}</h1>
                            <p><strong>Data de Lançamento:</strong> ${movie.release_date || 'Desconhecida'}</p>
                            <div class="movie-meta">
                                <p class="movie-meta-item">
                                    <strong>Língua:</strong> ${movie.original_language || 'Desconhecida'}
                                </p>
                                <p class="movie-meta-item">
                                    <strong>Género:</strong> ${movie.genre || 'Desconhecido'}
                                </p>
                            </div>
                            <p><strong>Sinopse:</strong> ${movie.overview || 'Sem sinopse.'}</p>
                            <div class="movie-rating">
                                <strong>Rating:</strong>
                                <span class="star">${stars}</span>
                                <span class="rating-value">(${movie.vote_average ? parseFloat(movie.vote_average).toFixed(1) : 'N/A'} / 10)</span>
                            </div>
                            <button class="delete-movie-btn" onclick="deleteMovie(${movie.id})">Apagar Filme</button>
                        </div>
                    </div>

                    <div class="user-reviews">
                        <h2>Comentários dos Utilizadores</h2>
                        <div id="comments-list">
                            ${commentsHTML || '<p>Nenhum comentário ainda.</p>'}
                        </div>
                    </div>
                </div>
            `;
        }

        // Apaga filme via API DELETE e redireciona para homepage
        function deleteMovie(movieId) {
            if (confirm('Tem certeza que deseja apagar este filme? Esta ação não pode ser desfeita.')) {
                // Chama endpoint DELETE /api/movies/{id}
                fetch(`/api/movies/${movieId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Erro ao apagar filme');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Success:', data);
                    alert('Filme apagado');
                    window.location.href = '/homepageadm';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao apagar filme. Tente novamente.');
                });
            }
        }

        // Apaga comentário via API DELETE
        async function deleteComment(commentId) {
            if (confirm('Tem certeza que deseja apagar este comentário?')) {
                try {
                    const response = await fetch(`/api/comments/${commentId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (response.ok) {
                        alert('Comentário apagado com sucesso');
                        location.reload(); // Recarrega a página para atualizar a lista
                    } else {
                        const data = await response.json();
                        alert('Erro: ' + (data.message || 'Erro ao apagar comentário'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Erro ao conectar com o servidor');
                }
            }
        }
    </script>
@endsection
