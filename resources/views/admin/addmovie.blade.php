{{-- Vista: Adicionar Filme - Formulário para criar novo filme via API --}}
@extends('layouts.app')

@section('content')
    <style>
        {!! file_get_contents(resource_path('views/admin/admin.css')) !!}
    </style>

    <div class="top-bar-box">
        <div class="top-bar">
            <div class="name-topbar">
                <button type="submit" class="nb-submit"
                        onclick="window.location.href='{{ url('/homepageadm') }}'">
                    Aprovação de Comentários</button>
                <button type="submit" class="nb-submit"
                        onclick="window.location.href='{{ url('/analytics') }}'">
                    Estatísticas</button>
            </div>

            <div class="top-bar-spacer"></div>
        </div>
    </div>

    {{-- Formulário de adição de filme --}}
    <div class="form-container">
        <h2 class="form-title">Adicionar Filme</h2>
        
        <div id="message"></div>
        
        <form id="addMovieForm" class="add-movie-form" aria-label="Adicionar Filme">
            <div>
                <label for="title" class="visually-hidden">Título do filme</label>
                <input
                    id="title"
                    name="title"
                    type="text"
                    required
                    placeholder="Título do filme"
                />
            </div>

            <div>
                <label for="poster_path" class="visually-hidden">Poster</label>
                <input
                    id="poster_path"
                    name="poster_path"
                    type="file"
                    accept="image/png,image/jpeg,image/jpg,image/gif"
                />
            </div>

            <div>
                <label for="release_date" class="visually-hidden">Data de Lançamento</label>
                <input
                    id="release_date"
                    class="date-input"
                    name="release_date"
                    type="date"
                    required
                    placeholder="Data de Lançamento"
                />
            </div>

            <div>
                <label for="original_language" class="visually-hidden">Idioma Original</label>
                <input
                    id="original_language"
                    name="original_language"
                    type="text"
                    required
                    placeholder="Idioma Original"
                />
            </div>

            <div>
                <label for="genre_ids" class="visually-hidden">Géneros</label>
                <select
                    id="genre_ids"
                    class="genre-input"
                    name="genre"
                    required
                    aria-label="Selecionar Género"
                >
                    <option value="" disabled selected>Seleccionar Género</option>
                    <option value="Ação">Ação</option>
                    <option value="Aventura">Aventura</option>
                    <option value="Animação">Animação</option>
                    <option value="Comédia">Comédia</option>
                    <option value="Crime">Crime</option>
                    <option value="Documentário">Documentário</option>
                    <option value="Drama">Drama</option>
                    <option value="Drama">Drama e Crime</option>
                    <option value="Família">Família</option>
                    <option value="Fantasia">Fantasia</option>
                    <option value="História">História</option>
                    <option value="Terror">Terror</option>
                    <option value="Música">Música</option>
                    <option value="Mistério">Mistério</option>
                    <option value="Romance">Romance</option>
                    <option value="Ficção Científica">Ficção Científica</option>
                    <option value="Cinema TV">Cinema TV</option>
                    <option value="Thriller">Thriller</option>
                    <option value="Guerra">Guerra</option>
                    <option value="Western">Western</option>
                </select>
            </div>

            <div>
                <label for="vote_average" class="visually-hidden">Rating</label>
                <input
                    id="vote_average"
                    class="rating-input"
                    name="vote_average"
                    type="number"
                    min="0"
                    max="10"
                    step="0.1"
                    required
                    placeholder="Rating"
                />
            </div>

            <div>
                <label for="overview" class="visually-hidden">Sinopse</label>
                <textarea
                    id="overview"
                    name="overview"
                    required
                    rows="4"
                    cols="50"
                    placeholder="Sinopse"
                ></textarea>
            </div>

            <button type="submit" class="submit-button" aria-label="Adicionar Filme">Adicionar</button>
        </form>
    </div>

    <script>
        // Submete formulário de adição de filme via API
        document.getElementById('addMovieForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Prepara dados do formulário incluindo ficheiro de poster
            const formData = new FormData();
            formData.append('title', document.getElementById('title').value);
            formData.append('release_date', document.getElementById('release_date').value);
            formData.append('original_language', document.getElementById('original_language').value);
            formData.append('genre', document.getElementById('genre_ids').value);
            formData.append('vote_average', document.getElementById('vote_average').value);
            formData.append('overview', document.getElementById('overview').value);
            
            const posterFile = document.getElementById('poster_path').files[0];
            if (posterFile) {
                formData.append('poster_path', posterFile);
            }
            
            const messageDiv = document.getElementById('message');
            
            try {
                // Envia dados para API de criação de filme
                const response = await fetch('/api/movies', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                // Exibe mensagem de sucesso ou erro
                if (response.ok) {
                    messageDiv.style.display = 'block';
                    messageDiv.style.background = '#d4edda';
                    messageDiv.style.color = '#155724';
                    messageDiv.textContent = 'Filme adicionado com sucesso!';
                    document.getElementById('addMovieForm').reset();
                    
                    setTimeout(() => {
                        messageDiv.style.display = 'none';
                    }, 3000);
                } else {
                    messageDiv.style.display = 'block';
                    messageDiv.style.background = '#f8d7da';
                    messageDiv.style.color = '#721c24';
                    messageDiv.textContent = 'Erro: ' + (data.errors ? Object.values(data.errors).flat().join(', ') : 'Erro ao adicionar filme');
                }
            } catch (error) {
                messageDiv.style.display = 'block';
                messageDiv.style.background = '#f8d7da';
                messageDiv.style.color = '#721c24';
                messageDiv.textContent = 'Erro ao conectar com o servidor';
                console.error('Error:', error);
            }
        });
    </script>
@endsection
