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
                    Aprovação de Avaliações</button>
                <button type="submit" class="nb-submit"
                        onclick="window.location.href='{{ url('/analytics') }}'">
                    Estatísticas</button>
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
                            <td>{{ $streamer['searches'] }}</td>
                            <td>{{ $streamer['comments'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($streamer['last_activity'])->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="analytics-empty-state">
                                Nenhum dado de streamers disponível
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
