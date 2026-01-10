<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StreamerController;
use Illuminate\Support\Facades\Route;

// Rota principal - redireciona baseado no role do utilizador ou para login
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('homepageadm');
        }
        return redirect()->route('homepage');
    }
    return redirect('/login');
});

// Rota de perfil requerida pelo Laravel Breeze
Route::get('/profile', function () {
    // Redireciona para login (substituir com página de perfil se necessário)
    return redirect()->route('login');
})->middleware(['auth'])->name('profile.edit');

// Rotas de Administrador - protegidas por middleware auth e admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/homepageadm', [AdminController::class, 'homepageadm'])->name('homepageadm'); // Homepage admin - aprovação de comentários
    Route::get('/loginadm', [AdminController::class, 'loginadm'])->name('loginadm'); // Login admin
    Route::get('/moviedetailsadm', [AdminController::class, 'moviedetailsadm'])->name('moviedetailsadm'); // Detalhes de filme admin
    Route::get('/addmovie', [AdminController::class, 'addmovie'])->name('addmovie'); // Formulário adicionar filme
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics'); // Estatísticas de utilizadores
});

// Rotas de Streamer - protegidas por middleware auth e streamer
Route::middleware(['auth', 'streamer'])->group(function () {
    Route::get('/homepage', [StreamerController::class, 'homepage'])->name('homepage'); // Homepage streamer - catálogo de filmes
    Route::get('/moviedetails', [StreamerController::class, 'moviedetails'])->name('moviedetails'); // Detalhes de filme streamer
    Route::post('/track-time', [StreamerController::class, 'trackTime'])->name('track.time'); // Tracking de tempo de sessão
});

Route::get('/loginstreamer', [StreamerController::class, 'loginstreamer'])->name('loginstreamer'); // Login streamer (rota pública)

// Rotas API de Comentários - autenticadas
use App\Http\Controllers\Api\CommentController;

// Rotas de comentários para utilizadores autenticados
Route::middleware('auth')->group(function () {
    Route::post('/api/comments', [CommentController::class, 'store']); // Criar comentário
    Route::delete('/api/comments/{id}', [CommentController::class, 'destroy']); // Apagar comentário próprio
});

// Rotas de gestão de comentários para administradores
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/api/comments/pending', [CommentController::class, 'pending']); // Listar comentários pendentes
    Route::post('/api/comments/{id}/approve', [CommentController::class, 'approve']); // Aprovar comentário
    Route::post('/api/comments/{id}/reject', [CommentController::class, 'reject']); // Rejeitar comentário
});

// Importa rotas de autenticação do Laravel Breeze
require __DIR__ . '/auth.php';
