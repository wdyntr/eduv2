<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\MapelController;
use App\Http\Controllers\Api\MateriController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\JurnalApiController;
use App\Http\Controllers\Api\RoleApiController;


// ========================================
// ADMIN AUTH
// ========================================

Route::middleware('api-session')->group(function () {

    Route::post('/admin/login', [AdminApiController::class, 'login']);

    Route::middleware('auth')->group(function () {

        Route::get('/admin/roles', [AdminApiController::class, 'getRoles']);
        Route::get('/admin/roles/manage', [RoleApiController::class, 'index']);
        Route::get('/admin/permissions', [RoleApiController::class, 'permissions']);

        Route::post('/admin/roles', [RoleApiController::class, 'store']);
        Route::put('/admin/roles/{id}', [RoleApiController::class, 'update']);
        Route::delete('/admin/roles/{id}', [RoleApiController::class, 'destroy']);

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

        Route::get('/admin/sekolah/{id}/kelas', [AdminApiController::class, 'sekolahKelasList']);
        Route::put('/admin/sekolah/{id}/kelas/{mapelId}', [AdminApiController::class, 'sekolahKelasUpdate']);

        // Mapel
        Route::post('/admin/mapel', [AdminApiController::class, 'tambahMapel']);
        Route::put('/admin/mapel/{id}', [AdminApiController::class, 'editMapel']);
        Route::delete('/admin/mapel/{id}', [AdminApiController::class, 'hapusMapel']);

        // Jurnal
        Route::get('/admin/jurnal/mine', [JurnalApiController::class, 'mine']);
        Route::post('/admin/jurnal', [JurnalApiController::class, 'store']);
        Route::post('/admin/jurnal/{id}/resubmit', [JurnalApiController::class, 'resubmit']);

        Route::get('/admin/jurnal/pending', [JurnalApiController::class, 'pending']);
        Route::get('/admin/jurnal/all', [JurnalApiController::class, 'allAdmin']);
        Route::post('/admin/jurnal/{id}/approve', [JurnalApiController::class, 'approve']);
        Route::put('/admin/jurnal/{id}/detail', [JurnalApiController::class, 'updateDetail']);
        Route::post('/admin/jurnal/{id}/reject', [JurnalApiController::class, 'reject']);
        Route::delete('/admin/jurnal/{id}', [JurnalApiController::class, 'destroy']);

        // Jurnal kategori
        Route::get('/admin/jurnal-kategori', [JurnalApiController::class, 'kategoriAdminList']);
        Route::post('/admin/jurnal-kategori', [JurnalApiController::class, 'kategoriStore']);
        Route::put('/admin/jurnal-kategori/{id}', [JurnalApiController::class, 'kategoriUpdate']);
        Route::delete('/admin/jurnal-kategori/{id}', [JurnalApiController::class, 'kategoriDestroy']);
    });
});


// ========================================
// PUBLIC API
// ========================================

Route::get('/mapel', [MapelController::class, 'index']);
Route::get('/materi', [MateriController::class, 'index']);
Route::get('/classroom', [ClassroomController::class, 'index']);
Route::get('/classroom/{id}', [ClassroomController::class, 'show']);

Route::get('/jurnal', [JurnalApiController::class, 'index']);
Route::get('/jurnal-kategori', [JurnalApiController::class, 'kategori']);
Route::get('/jurnal/{id}', [JurnalApiController::class, 'show']);