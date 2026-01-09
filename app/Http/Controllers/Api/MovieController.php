<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    /**
     * GET /api/movies
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::latest()->get();
        return response()->json($movies);
    }

    /**
     * GET /api/movies/search?q={query}
     * Search movies by title.
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (empty($query)) {
            return response()->json([]);
        }
        
        $movies = Movie::where('title', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'title', 'poster_path']);
        
        return response()->json($movies);
    }


    /**
     * POST /api/movies
     * Store a newly created resource in storage.
     */
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

    /**
     * GET /api/movies/{id}
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $movie = Movie::with('comments')->find($id);
        
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }
        
        return response()->json($movie);
    }

    /**
     * PUT/PATCH /api/movies/{id}
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * DELETE /api/movies/{id}
     * Remove the specified resource from storage.
     */
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
