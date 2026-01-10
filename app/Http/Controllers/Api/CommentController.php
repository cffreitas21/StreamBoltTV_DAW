<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // GET http://localhost:8000/api/comments/pending
    // Retorna lista de comentários pendentes para aprovação (admin)
    public function pending()
    {
        $comments = Comment::with(['user', 'movie'])
            ->where('approved', false)
            ->latest()
            ->get();
        
        return response()->json($comments);
    }

    // GET http://localhost:8000/api/comments/approved/{movieId}
    // Retorna todos os comentários aprovados de um filme
    public function approved($movieId)
    {
        $comments = Comment::with('user')
            ->where('movie_id', $movieId)
            ->where('approved', true)
            ->latest()
            ->get();
        
        return response()->json($comments);
    }

    // POST http://localhost:8000/api/comments
    // Cria novo comentário (sempre pendente até aprovação)
    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'comment' => 'required|string|max:1000'
        ]);

        $comment = Comment::create([
            'movie_id' => $request->movie_id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'approved' => false, // Sempre começa como pendente
        ]);

        $comment->load('user');

        // Registra evento de comentário no analytics
        $this->trackAnalytics('comment');

        return response()->json([
            'message' => 'Comentário enviado para aprovação',
            'comment' => $comment
        ], 201);
    }

    // Regista evento no sistema de analytics
    private function trackAnalytics($type)
    {
        $analyticsFile = storage_path('app/analytics.json');
        
        if (!file_exists($analyticsFile)) {
            file_put_contents($analyticsFile, json_encode([]));
        }
        
        $analytics = json_decode(file_get_contents($analyticsFile), true);
        
        $analytics[] = [
            'user_id' => auth()->id(),
            'name' => auth()->user()->name,
            'type' => $type,
            'timestamp' => now()->toDateTimeString(),
        ];
        
        file_put_contents($analyticsFile, json_encode($analytics));
    }

    // POST http://localhost:8000/api/comments/{id}/approve
    // Aprova um comentário (admin)
    public function approve($id)
    {
        $comment = Comment::find($id);
        
        if (!$comment) {
            return response()->json(['message' => 'Comentário não encontrado'], 404);
        }
        
        $comment->approved = true;
        $comment->save();
        
        return response()->json([
            'message' => 'Comentário aprovado',
            'comment' => $comment
        ]);
    }

    // POST http://localhost:8000/api/comments/{id}/reject
    // Rejeita/apaga um comentário (admin)
    public function reject($id)
    {
        $comment = Comment::find($id);
        
        if (!$comment) {
            return response()->json(['message' => 'Comentário não encontrado'], 404);
        }
        
        $comment->delete();
        
        return response()->json(['message' => 'Comentário rejeitado e removido']);
    }

    // DELETE http://localhost:8000/api/comments/{id}
    // Apaga comentário próprio ou qualquer comentário (admin)
    public function destroy($id)
    {
        $comment = Comment::find($id);
        
        if (!$comment) {
            return response()->json(['message' => 'Comentário não encontrado'], 404);
        }
        
        // Verifica se é o dono do comentário ou admin
        if ($comment->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Não autorizado'], 403);
        }
        
        $comment->delete();
        
        return response()->json(['message' => 'Comentário apagado']);
    }
}
