<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminController;

// Public
Route::get('/', [PublicController::class, 'homepage']);
Route::get('/media', [PublicController::class, 'media']);
Route::get('/media/{jenjang}', [PublicController::class, 'mediaJenjang']);
Route::get('/media/{jenjang}/{materi_id}', [PublicController::class, 'mediaDetail']);
Route::get('/classroom', [PublicController::class, 'classroom']);

// Admin Pages
Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/materi', [AdminController::class, 'materi']);
    Route::get('/materi/tambah', [AdminController::class, 'materiTambah']);
    Route::get('/materi/edit/{id}', [AdminController::class, 'materiEdit']);
    Route::get('/classroom', [AdminController::class, 'classroom']);
    Route::get('/mapel', [AdminController::class, 'mapel']);
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/profile', [AdminController::class, 'profile']);
    Route::get('/logout', [AdminController::class, 'logout']);
});