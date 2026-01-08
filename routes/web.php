<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StreamerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.app');
});


Route::get('/homepageadm', [AdminController::class, 'homepageadm'])->name('homepageadm');
Route::get('/loginadm', [AdminController::class, 'loginadm'])->name('loginadm');
Route::get('/moviedetailsadm', [AdminController::class, 'moviedetailsadm'])->name('moviedetailsadm');
Route::get('/addmovie', [AdminController::class, 'addmovie'])->name('addmovie');

Route::get('/loginstreamer', [StreamerController::class, 'loginstreamer'])->name('loginstreamer');
Route::get('/homepage', [StreamerController::class, 'homepage'])->name('homepage');
Route::get('/moviedetails', [StreamerController::class, 'moviedetails'])->name('moviedetails');


