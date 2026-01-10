{{-- Vista: Homepage do Admin - Aprovação de avaliações com pesquisa --}}
@extends('layouts.app')

@section('content')
    <style>
        {!! file_get_contents(resource_path('views/admin/admin.css')) !!}
    </style>

    {{-- Barra superior com navegação e pesquisa --}}
    <div class="top-bar-box">
        <div class="top-bar">
            <div class="name-topbar">
                <button type="submit" class="nb-submit"
                        onclick="window.location.href='{{ url('/addmovie') }}'">
                    Adicionar Filme</button>

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
        <h1>Aprovação de Comentários</h1>
    </div>

    {{-- Lista de comentários pendentes para aprovação --}}
    <div class="comments-approval-container">
        <div class="loading-message" id="loadingMessage">A carregar comentários...</div>
        <div class="empty-message" id="emptyMessage" style="display: none; text-align: center; padding: 20px;">Sem comentários para aprovar.</div>
        
        {{-- Esta div será preenchida dinamicamente com os comentários --}}
        <ul class="comments-list-adm" id="commentsList"></ul>
    </div>

    <script>
        // Pesquisa de filmes com autocomplete (igual ao streamer)
        const searchInput = document.getElementById('search-input');
        const searchDropdown = document.getElementById('search-dropdown');
        let searchTimeout;

        // Debounce de 300ms para evitar chamadas excessivas à API
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
                    
                    // Resultados redirecionam para moviedetailsadm
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

        // Carrega comentários pendentes de aprovação da API
        async function loadPendingComments() {
            const loadingMessage = document.getElementById('loadingMessage');
            const emptyMessage = document.getElementById('emptyMessage');
            const commentsList = document.getElementById('commentsList');
            
            try {
                const response = await fetch('/api/comments/pending');
                const comments = await response.json();
                
                loadingMessage.style.display = 'none';
                
                if (comments.length === 0) {
                    emptyMessage.style.display = 'block';
                    return;
                }
                
                // Gera cards de comentários dinamicamente
                commentsList.innerHTML = comments.map(comment => {
                    const userName = comment.user ? comment.user.name : 'Utilizador';
                    const movieTitle = comment.movie ? comment.movie.title : 'Filme';
                    const posterPath = comment.movie && comment.movie.poster_path 
                        ? `/storage/${comment.movie.poster_path}` 
                        : '';
                    const userInitial = userName.charAt(0).toUpperCase();
                    
                    return `
                        <li class="comment-card-adm" data-comment-id="${comment.id}">
                            <div class="comment-content-adm">
                                <div class="comment-header-adm">
                                    <div class="icon-letra">${userInitial}</div>
                                    <div class="comment-user-info">
                                        <div class="user-name-adm">${userName}</div>
                                        <div class="movie-title-adm">${movieTitle}</div>
                                    </div>
                                </div>
                                <div class="comment-text-adm">
                                    ${comment.comment}
                                </div>
                                <div class="comment-actions-adm">
                                    <select class="action-select-adm" onchange="updateButtonColor(this, ${comment.id})">
                                        <option value="">Selecionar ação</option>
                                        <option value="aprovar">Aprovar</option>
                                        <option value="naoaprovar">Não Aprovar</option>
                                    </select>
                                    <button class="submit-btn-adm" id="submit-btn-${comment.id}" onclick="handleCommentAction(${comment.id})">Submeter</button>
                                </div>
                            </div>
                            ${posterPath ? `<img src="${posterPath}" alt="Poster" class="movie-poster-adm" />` : ''}
                        </li>
                    `;
                }).join('');
                
            } catch (error) {
                console.error('Erro ao carregar comentários:', error);
                loadingMessage.textContent = 'Erro ao carregar comentários.';
            }
        }
        
        // Handler para aprovar/rejeitar comentário
        async function handleCommentAction(commentId) {
            const card = document.querySelector(`[data-comment-id="${commentId}"]`);
            const select = card.querySelector('.action-select-adm');
            const action = select.value;
            
            if (!action) {
                alert('Por favor, selecione uma ação.');
                return;
            }
            
            const endpoint = action === 'aprovar' 
                ? `/api/comments/${commentId}/approve` 
                : `/api/comments/${commentId}/reject`;
            
            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    card.remove();
                    
                    // Verifica se ainda há comentários
                    const remainingComments = document.querySelectorAll('.comment-card-adm');
                    if (remainingComments.length === 0) {
                        document.getElementById('emptyMessage').style.display = 'block';
                    }
                } else {
                    const data = await response.json();
                    alert('Erro: ' + (data.message || 'Erro ao processar ação'));
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao conectar com o servidor');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadPendingComments();
        });

        // Atualiza a cor do botão baseado na seleção
        function updateButtonColor(selectElement, commentId) {
            const button = document.getElementById(`submit-btn-${commentId}`);
            const selectedValue = selectElement.value;
            
            if (selectedValue === 'aprovar') {
                button.style.backgroundColor = 'green';
                button.style.color = 'white';
            } else if (selectedValue === 'naoaprovar') {
                button.style.backgroundColor = 'red';
                button.style.color = 'white';
            } else {
                button.style.backgroundColor = 'dodgerblue';
                button.style.color = 'white';
            }
        }

        // Handler para mudança de seleção no dropdown de ações
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('action-select-adm')) {
                const button = e.target.nextElementSibling;
                button.className = 'submit-btn-adm';
                
                if (e.target.value === 'aprovar') {
                    button.classList.add('approve-adm');
                } else if (e.target.value === 'naoaprovar') {
                    button.classList.add('reject-adm');
                }
            }
        });
    </script>
@endsection
