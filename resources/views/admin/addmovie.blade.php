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
                    Aprovação de Avaliações</button>
                <button type="submit" class="nb-submit"
                        onclick="window.location.href='{{ url('/analytics') }}'">
                    Estatísticas</button>
            </div>

            <div class="top-bar-spacer" style="flex:1;"></div>
        </div>
    </div>

    <div class="form-container">
        <h2 class="form-title">Adicionar Filme</h2>
        
        <form class="add-movie-form" aria-label="Adicionar Filme">
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
                    accept="image/png"
                    required
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
                    name="genre_ids"
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

    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-title {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .add-movie-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .add-movie-form input,
        .add-movie-form select,
        .add-movie-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .add-movie-form textarea {
            resize: vertical;
            font-family: inherit;
        }

        .add-movie-form input[type="file"] {
            padding: 8px;
        }

        .submit-button {
            padding: 12px 24px;
            background: dodgerblue;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .submit-button:hover {
            background: #1e90ff;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
@endsection
