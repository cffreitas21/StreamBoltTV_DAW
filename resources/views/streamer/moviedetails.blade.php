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
        </div>
    </div>

    <main class="center-content" id="mainContent">
        <div class="loading-text">A carregar...</div>
    </main>

    <script>
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

        function renderMovieDetails(movie, comments) {
            const mainContent = document.getElementById('mainContent');
            const currentUserId = {{ auth()->id() }};
            
            const posterHTML = movie.poster_path 
                ? `<img class="movie-poster" src="/storage/${movie.poster_path}" alt="${movie.title}">`
                : `<div class="no-poster">No Image</div>`;

            const voteAverage = parseFloat(movie.vote_average) || 0;
            const stars = Array.from({ length: 5 }, (_, i) => 
                voteAverage / 2 >= i + 1 ? '★' : '☆'
            ).join('');

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

        function submitComment(event) {
            event.preventDefault();
            
            const textarea = document.getElementById('comment-textarea');
            const message = document.getElementById('comment-message');
            
            // Quando o endpoint estiver pronto:
            // const movieId = new URLSearchParams(window.location.search).get('id');
            // fetch('/api/comments', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({
            //         movie_id: movieId,
            //         comment: textarea.value
            //     })
            // });
            
            message.textContent = 'Comentário enviado com sucesso!';
            textarea.value = '';
            
            setTimeout(() => {
                message.textContent = '';
            }, 3000);
        }

        function deleteComment(commentId) {
            if (confirm('Tem certeza que deseja apagar este comentário?')) {
                // Quando o endpoint estiver pronto:
                // fetch(`/api/comments/${commentId}`, { method: 'DELETE' });
                
                alert('Comentário apagado!');
                location.reload();
            }
        }
    </script>
@endsection
