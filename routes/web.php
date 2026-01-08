<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StreamerController;
use Illuminate\Support\Facades\Route;

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

// provide the named route expected by Breeze navigation
Route::get('/profile', function () {
    // replace this redirect with a view or controller that shows the user's profile
    return redirect()->route('login');
})->middleware(['auth'])->name('profile.edit');

// Admin routes - protected by admin middleware
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/homepageadm', [AdminController::class, 'homepageadm'])->name('homepageadm');
    Route::get('/loginadm', [AdminController::class, 'loginadm'])->name('loginadm');
    Route::get('/moviedetailsadm', [AdminController::class, 'moviedetailsadm'])->name('moviedetailsadm');
    Route::get('/addmovie', [AdminController::class, 'addmovie'])->name('addmovie');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    
    // Delete routes
    Route::delete('/movies/{id}', [AdminController::class, 'deleteMovie'])->name('movies.delete');
    Route::delete('/comments/{id}', [AdminController::class, 'deleteComment'])->name('comments.delete');
    Route::post('/comments/{id}/approve', [AdminController::class, 'approveComment'])->name('comments.approve');
});

// Streamer routes - protected by streamer middleware
Route::middleware(['auth', 'streamer'])->group(function () {
    Route::get('/homepage', [StreamerController::class, 'homepage'])->name('homepage');
    Route::get('/moviedetails', [StreamerController::class, 'moviedetails'])->name('moviedetails');
    Route::post('/track-time', [StreamerController::class, 'trackTime'])->name('track.time');
});

Route::get('/loginstreamer', [StreamerController::class, 'loginstreamer'])->name('loginstreamer');


require __DIR__ . '/auth.php';
