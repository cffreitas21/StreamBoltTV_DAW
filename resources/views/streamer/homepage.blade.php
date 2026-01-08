@extends('layouts.app')

@section('content')
    <style>
        {!! file_get_contents(resource_path('views/admin/admin.css')) !!}
    </style>

    <div class="top-bar-box">
        <div class="top-bar">
            <div class="name-topbar">
                <button></button>
            </div>

            <div class="top-bar-spacer" style="flex:1;"></div>

        </div>
    </div>


    PAGINA INICIAL DO STREAMER
@endsection
