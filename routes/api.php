<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\MapelController;
use App\Http\Controllers\Api\MateriController;
use App\Http\Controllers\Api\ClassroomController;

// Auth — tidak perlu middleware
Route::post('/admin/login', [AdminApiController::class, 'login']);

// Admin API — butuh auth
Route::middleware('admin.auth')->group(function () {
    // Users
    Route::get('/admin/users', [AdminApiController::class, 'getUsers']);
    Route::post('/admin/users', [AdminApiController::class, 'tambahAdmin']);
    Route::delete('/admin/users/{id}', [AdminApiController::class, 'hapusAdmin']);

    // Profile
    Route::put('/admin/profile', [AdminApiController::class, 'updateProfile']);

    // Materi
    Route::post('/admin/materi', [AdminApiController::class, 'tambahMateri']);
    Route::put('/admin/materi/{id}', [AdminApiController::class, 'editMateri']);
    Route::delete('/admin/materi/{id}', [AdminApiController::class, 'hapusMateri']);

    // Classroom
    Route::post('/admin/classroom', [AdminApiController::class, 'tambahSekolah']);
    Route::put('/admin/classroom/{id}', [AdminApiController::class, 'editSekolah']);
    Route::delete('/admin/classroom/{id}', [AdminApiController::class, 'hapusSekolah']);

    // Mapel
    Route::post('/admin/mapel', [AdminApiController::class, 'tambahMapel']);
    Route::put('/admin/mapel/{id}', [AdminApiController::class, 'editMapel']);
    Route::delete('/admin/mapel/{id}', [AdminApiController::class, 'hapusMapel']);
});

// Public API
Route::get('/mapel', [MapelController::class, 'index']);
Route::get('/materi', [MateriController::class, 'index']);
Route::get('/classroom', [ClassroomController::class, 'index']);