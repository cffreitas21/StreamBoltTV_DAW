<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AnalyticsHelper;

class MovieController extends Controller
{
    // GET http://localhost:8000/api/movies
    // Retorna lista de todos os filmes
    public function index()
    {
        $movies = Movie::latest()->get();
        return response()->json($movies);
    }

    // GET http://localhost:8000/api/movies/search?q={query}
    // Pesquisa filmes por título (máximo 5 resultados)
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (empty($query)) {
            return response()->json([]);
        }
        
        // Regista pesquisa no analytics se utilizador autenticado
        if (auth()->check() && auth()->user()->isStreamer()) {
            AnalyticsHelper::trackSearch(
                auth()->id(),
                auth()->user()->name,
                $query
            );
        }
        
        $movies = Movie::where('title', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'title', 'poster_path']);
        
        return response()->json($movies);
    }


    // POST http://localhost:8000/api/movies
    // Cria novo filme com upload de poster
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'poster_path' => 'nullable|file|max:2048',
            'release_date' => 'required|date',
            'original_language' => 'required|string|max:10',
            'genre' => 'required|string|max:100',
            'vote_average' => 'required|numeric|min:0|max:10',
            'overview' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // Handle poster upload
        if ($request->hasFile('poster_path')) {
            $path = $request->file('poster_path')->store('posters', 'public');
            $data['poster_path'] = $path;
        }

        $movie = Movie::create($data);

        return response()->json([
            'message' => 'Movie created successfully',
            'movie' => $movie
        ], 201);
    }

    // GET http://localhost:8000/api/movies/{id}
    // Retorna detalhes do filme com comentários (apenas aprovados para streamers)
    public function show(string $id)
    {
        $movie = Movie::with(['comments' => function($query) {
            // Admins veem todos os comentários, streamers apenas os aprovados
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                $query->where('approved', true);
            }
            $query->with('user')->latest();
        }])->find($id);
        
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }
        
        return response()->json($movie);
    }

    // PUT/PATCH http://localhost:8000/api/movies/{id}
    // Atualiza informações do filme
    public function update(Request $request, string $id)
    {
        //
    }

    // DELETE http://localhost:8000/api/movies/{id}
    // Apaga filme e poster do storage
    public function destroy(string $id)
    {
        $movie = Movie::find($id);
        
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }
        
        // Delete poster file if exists
        if ($movie->poster_path) {
            Storage::disk('public')->delete($movie->poster_path);
        }
        
        // Delete movie (comments will be deleted by cascade)
        $movie->delete();
        
        return response()->json(['message' => 'Movie deleted successfully'], 200);
    }
}
