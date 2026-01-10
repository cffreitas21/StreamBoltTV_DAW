{{-- Vista: Detalhes do Filme (Streamer) - Informações do filme e comentários --}}
@extends('layouts.app')

@section('content')
    <style>
        {!! file_get_contents(resource_path('views/streamer/streamer.css')) !!}
    </style>

    <div class="top-bar-box">
        <div class="top-bar">
            <div class="name-topbar">
                <button type="submit" class="nb-submit"
                        onclick="window.location.href='{{ url('/homepage') }}'">
                    Ver Filmes Recomendados</button>
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

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.remove('show');
            }
        });

        // Carrega detalhes do filme ao iniciar página
        document.addEventListener('DOMContentLoaded', async function() {
            const mainContent = document.getElementById('mainContent');
            // Extraí ID do filme da URL
            const urlParams = new URLSearchParams(window.location.search);
            const movieId = urlParams.get('id');

            if (!movieId) {
                mainContent.innerHTML = '<div class="loading-text">ID do filme não encontrado</div>';
                return;
            }

            try {
                // Busca detalhes do filme e comentários da API
                const response = await fetch(`/api/movies/${movieId}`);
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    const errorData = await response.text();
                    console.error('Error response:', errorData);
                    throw new Error('Filme não encontrado');
                }
                
                const movie = await response.json();
                console.log('Movie data:', movie);
                
                const comments = movie.comments || [];

                renderMovieDetails(movie, comments);

            } catch (error) {
                console.error('Error:', error);
                mainContent.innerHTML = '<div class="loading-text">Erro ao carregar filme: ' + error.message + '</div>';
            }
        });

        // Renderiza informações do filme e comentários no DOM
        function renderMovieDetails(movie, comments) {
            const mainContent = document.getElementById('mainContent');
            const currentUserId = {{ auth()->id() }};
            
            const posterHTML = movie.poster_path 
                ? `<img class="movie-poster" src="/storage/${movie.poster_path}" alt="${movie.title}">`
                : `<div class="no-poster">No Image</div>`;

            // Converte rating numérico em estrelas (1-5)
            const voteAverage = parseFloat(movie.vote_average) || 0;
            const stars = Array.from({ length: 5 }, (_, i) => 
                voteAverage / 2 >= i + 1 ? '★' : '☆'
            ).join('');

            // Gera HTML dos comentários com botão de delete para o autor
            const commentsHTML = comments.map(c => `
                <div class="review-card">
                    <div class="review-header">
                        <div class="icon-letra">${c.user?.name ? c.user.name.charAt(0).toUpperCase() : 'U'}</div>
                        <span class="review-author">${c.user?.name || 'Utilizador'}</span>
                        <span class="review-date">${new Date(c.created_at).toLocaleDateString('pt-PT')}</span>
                        ${c.user_id == currentUserId ? `
                            <button class="delete-btn" onclick="deleteComment(${c.id})">Apagar Comentário</button>
                        ` : ''}
                    </div>
                    <div class="review-content">${c.comment}</div>
                </div>
            `).join('');

            //HTML DINAMICO POR CAUSA DE SER ASSINCRONO********************
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
                        </div>
                    </div>

                    <form onsubmit="submitComment(event)" class="comment-form">
                        <div class="icon-letra">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <br>
                        <label for="comment-textarea" class="visually-hidden">Adicionar comentário</label>
                        <textarea
                            id="comment-textarea"
                            placeholder="Adicionar comentário..."
                            rows="8"
                            required
                        ></textarea>
                        <br>
                        <button type="submit" class="submit-btn-comment">Comentar</button>
                        <div id="comment-message"></div>
                    </form>

                    <div class="user-reviews">
                        <h2>Comentários dos Utilizadores</h2>
                        <div id="comments-list">
                            ${commentsHTML || '<p>Nenhum comentário ainda. Seja o primeiro a comentar!</p>'}
                        </div>
                    </div>
                </div>
            `;
        }

        // Submete novo comentário para aprovação via API
        async function submitComment(event) {
            event.preventDefault();
            
            const textarea = document.getElementById('comment-textarea');
            const message = document.getElementById('comment-message');
            const movieId = new URLSearchParams(window.location.search).get('id');
            
            if (!textarea.value.trim()) {
                message.style.color = 'red';
                message.textContent = 'Por favor, escreva um comentário.';
                return;
            }
            
            try {
                const response = await fetch('/api/comments', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        movie_id: movieId,
                        comment: textarea.value
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    message.style.color = 'green';
                    message.textContent = 'Comentário enviado para aprovação!';
                    textarea.value = '';
                } else {
                    message.style.color = 'red';
                    message.textContent = 'Erro: ' + (data.message || 'Erro ao enviar comentário');
                }
            } catch (error) {
                console.error('Error:', error);
                message.style.color = 'red';
                message.textContent = 'Erro ao conectar com o servidor';
            }
            
            setTimeout(() => {
                message.textContent = '';
            }, 5000);
        }

        // Apaga comentário do próprio utilizador via API DELETE
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
