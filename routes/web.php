<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\JurnalController;

use App\Http\Controllers\ThumbnailProxyController;
use App\Http\Controllers\Api\JurnalFileController;

// ======================================================
// PUBLIC PAGES
// ======================================================

Route::get('/', [PublicController::class, 'homepage']);

Route::get('/classroom', [PublicController::class, 'classroom']);
Route::get('/classroom/{id}', [PublicController::class, 'classroomDetail']);

Route::get('/media', [PublicController::class, 'media']);
Route::get('/media/{jenjang}', [PublicController::class, 'mediaJenjang']);
Route::get('/media/{jenjang}/{materi_id}', [PublicController::class, 'mediaDetail']);

Route::get('/artikel', [PublicController::class, 'artikel']);
Route::get('/artikel/kategori/{slug}', [PublicController::class, 'artikelKategori']);
Route::get('/artikel/{slug}', [PublicController::class, 'artikelDetail']);

Route::get('/jurnal', [JurnalController::class, 'index']);
Route::get('/jurnal/{id}', [JurnalController::class, 'show']);
// === DINONAKTIFKAN SEMENTARA — menunggu data asli & validasi algoritma ===
// Route::get('/prediksi-ptn', [PublicController::class, 'prediksiPtn'])->name('prediksi.ptn');
// Route::post('/prediksi-ptn', [PublicController::class, 'prediksiPtn'])->name('prediksi.ptn.submit');
// === AKTIFKAN KEMBALI SETELAH DATA SIAP ===
Route::get('/thumbnail-proxy/{id}', [ThumbnailProxyController::class, 'show']);

// ======================================================
// JURNAL FILE DOWNLOAD (publik untuk approved, auth untuk belum approved)
// ======================================================

Route::get('/jurnal/{id}/file/{type?}', [JurnalFileController::class, 'download'])
    ->name('jurnal.download');

// ======================================================
// ADMIN PAGES / UI + PROTECTED ROUTES
// ======================================================

Route::middleware('auth')->group(function () {

    Route::get('/admin', [
        AdminController::class,
        'dashboard'
    ])->name('user.dashboard');

    Route::get('/admin/profile', [
        AdminController::class,
        'profile'
    ]);

    Route::get('/admin/users', [
        AdminController::class,
        'users'
    ]);

    Route::get('/admin/roles', [
        AdminController::class,
        'roles'
    ]);

    Route::get('/admin/materi', [
        AdminController::class,
        'materi'
    ]);

    Route::get('/admin/materi/tambah', [
        AdminController::class,
        'materiTambah'
    ]);

    Route::get('/admin/materi/edit/{id}', [
        AdminController::class,
        'materiEdit'
    ]);

    Route::get('/admin/artikel', [
        AdminController::class,
        'artikel'
    ]);

    Route::get('/admin/artikel/tambah', [
        AdminController::class,
        'artikelTambah'
    ]);

    Route::get('/admin/artikel/edit/{id}', [
        AdminController::class,
        'artikelEdit'
    ]);

    Route::get('/admin/mapel', [
        AdminController::class,
        'mapel'
    ]);

    Route::get('/admin/classroom', [
        AdminController::class,
        'classroom'
    ]);

    Route::get('/admin/classroom/{id}', [
        AdminController::class,
        'classroomDetail'
    ]);

    Route::get('/admin/jurnal', [
        AdminController::class,
        'jurnal'
    ]);

    Route::post('/admin/logout', [
        AdminController::class,
        'logout'
    ]);
});
