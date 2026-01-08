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
                        onclick="window.location.href='{{ url('/analytics') }}'">
                    Estatísticas</button>
            </div>

            <div class="top-bar-spacer" style="flex:1;"></div>

            <form method="GET" class="search-container">
                <input
                    id="{{ $inputId ?? 'search-input' }}"
                    name="query"
                    type="text"
                    placeholder="Pesquisar Filmes..."
                    value="{{ old('query', $query ?? '') }}"
                    aria-label="Pesquisar Filmes"
                />
                <button type="submit" class="visually-hidden">Pesquisar</button>
            </form>
        </div>
    </div>
    <div class="center-title">
        <h1>Aprovação de Avaliações</h1>
    </div>
    HOMEPAGE DO ADMIN
@endsection
