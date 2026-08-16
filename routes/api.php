<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\RoleApiController;
use App\Http\Controllers\Api\MateriApiController;
use App\Http\Controllers\Api\ArtikelApiController;
use App\Http\Controllers\Api\MapelApiController;
use App\Http\Controllers\Api\ClassroomApiController;
use App\Http\Controllers\Api\JurnalApiController;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\MapelController;
use App\Http\Controllers\Api\MateriController;
use App\Http\Controllers\Api\ArtikelController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\SekolahApiController;

// ======================================================
// SESSION API
// ======================================================

Route::middleware('api-session')->group(function () {

    // ==================================================
    // AUTH
    // ==================================================

    Route::post('/auth/login', [
        AuthApiController::class,
        'login'
    ]);

    Route::middleware('auth')->group(function () {

        Route::post('/auth/logout', [
            AuthApiController::class,
            'logout'
        ]);

        Route::get('/auth/me', [
            AuthApiController::class,
            'me'
        ]);


        // ==================================================
        // PROFILE
        // ==================================================

        Route::get('/profile', [
            ProfileApiController::class,
            'show'
        ]);

        Route::put('/profile', [
            ProfileApiController::class,
            'update'
        ]);


        // ==================================================
        // USERS
        // ==================================================

        Route::get('/users', [
            UserApiController::class,
            'index'
        ]);

        Route::post('/users', [
            UserApiController::class,
            'store'
        ]);

        Route::delete('/users/{id}', [
            UserApiController::class,
            'destroy'
        ]);


        // ==================================================
        // ROLES
        // ==================================================

        Route::get('/roles', [
            RoleApiController::class,
            'index'
        ]);

        Route::get('/roles/options', [
            RoleApiController::class,
            'options'
        ]);

        Route::post('/roles', [
            RoleApiController::class,
            'store'
        ]);

        Route::put('/roles/{id}', [
            RoleApiController::class,
            'update'
        ]);

        Route::delete('/roles/{id}', [
            RoleApiController::class,
            'destroy'
        ]);

        Route::get('/permissions', [
            RoleApiController::class,
            'permissions'
        ]);


        // ==================================================
        // MATERI
        // ==================================================

        Route::post('/materi', [
            MateriApiController::class,
            'store'
        ]);

        Route::put('/materi/{id}', [
            MateriApiController::class,
            'update'
        ]);

        Route::delete('/materi/{id}', [
            MateriApiController::class,
            'destroy'
        ]);


        // ==================================================
        // ARTIKEL
        // ==================================================

        Route::get('/artikel/manage', [
            ArtikelApiController::class,
            'index'
        ]);

        Route::post('/artikel', [
            ArtikelApiController::class,
            'store'
        ]);

        Route::put('/artikel/{id}', [
            ArtikelApiController::class,
            'update'
        ]);

        Route::delete('/artikel/{id}', [
            ArtikelApiController::class,
            'destroy'
        ]);

        Route::get('/artikel-kategori/manage', [
            ArtikelApiController::class,
            'kategoriList'
        ]);

        Route::post('/artikel-kategori', [
            ArtikelApiController::class,
            'kategoriStore'
        ]);

        Route::put('/artikel-kategori/{id}', [
            ArtikelApiController::class,
            'kategoriUpdate'
        ]);

        Route::delete('/artikel-kategori/{id}', [
            ArtikelApiController::class,
            'kategoriDestroy'
        ]);


        // ==================================================
        // MAPEL
        // ==================================================

        Route::post('/mapel', [
            MapelApiController::class,
            'store'
        ]);

        Route::put('/mapel/{id}', [
            MapelApiController::class,
            'update'
        ]);

        Route::delete('/mapel/{id}', [
            MapelApiController::class,
            'destroy'
        ]);


        // ==================================================
        // SEKOLAH - CRUD
        // Permission: sistem.kelola
        // ==================================================

        Route::get('/sekolah', [
            SekolahApiController::class,
            'index'
        ]);

        Route::get('/sekolah/{id}', [
            SekolahApiController::class,
            'show'
        ]);

        Route::post('/sekolah', [
            SekolahApiController::class,
            'store'
        ]);

        Route::put('/sekolah/{id}', [
            SekolahApiController::class,
            'update'
        ]);

        Route::delete('/sekolah/{id}', [
            SekolahApiController::class,
            'destroy'
        ]);


        // ==================================================
        // CLASSROOM - KONFIGURASI
        // Permission: classroom.kelola
        // ==================================================

        Route::get('/sekolah/{id}/kelas', [
            ClassroomApiController::class,
            'kelas'
        ]);

        Route::put('/sekolah/{id}/kelas/{mapelId}', [
            ClassroomApiController::class,
            'updateKelas'
        ]);

        Route::get('/kota-kabupaten', [
            LocationApiController::class,
            'kotaKabupaten'
        ]);


        // ==================================================
        // JURNAL - USER
        // ==================================================

        Route::get('/jurnal/mine', [
            JurnalApiController::class,
            'mine'
        ]);

        Route::post('/jurnal', [
            JurnalApiController::class,
            'store'
        ]);

        Route::post('/jurnal/{id}/resubmit', [
            JurnalApiController::class,
            'resubmit'
        ]);


        // ==================================================
        // JURNAL - REVIEW
        // ==================================================

        Route::get('/jurnal/pending', [
            JurnalApiController::class,
            'pending'
        ]);

        Route::get('/jurnal/all', [
            JurnalApiController::class,
            'allAdmin'
        ]);

        Route::post('/jurnal/{id}/approve', [
            JurnalApiController::class,
            'approve'
        ]);

        Route::put('/jurnal/{id}/detail', [
            JurnalApiController::class,
            'updateDetail'
        ]);

        Route::post('/jurnal/{id}/reject', [
            JurnalApiController::class,
            'reject'
        ]);

        Route::delete('/jurnal/{id}', [
            JurnalApiController::class,
            'destroy'
        ]);


        // ==================================================
        // JURNAL KATEGORI - MANAGEMENT
        // ==================================================

        /*
         * GET dipisahkan dari public /jurnal-kategori
         * agar tidak terjadi benturan route.
         */
        Route::get('/jurnal-kategori/manage', [
            JurnalApiController::class,
            'kategoriAdminList'
        ]);

        Route::post('/jurnal-kategori', [
            JurnalApiController::class,
            'kategoriStore'
        ]);

        Route::put('/jurnal-kategori/{id}', [
            JurnalApiController::class,
            'kategoriUpdate'
        ]);

        Route::delete('/jurnal-kategori/{id}', [
            JurnalApiController::class,
            'kategoriDestroy'
        ]);
    });
});


// ======================================================
// PUBLIC API
// ======================================================

Route::get('/mapel', [
    MapelController::class,
    'index'
]);

Route::get('/materi', [
    MateriController::class,
    'index'
]);

Route::get('/artikel-kategori', [
    ArtikelController::class,
    'kategori'
]);

Route::get('/artikel', [
    ArtikelController::class,
    'index'
]);

Route::get('/artikel/{slug}', [
    ArtikelController::class,
    'show'
]);

Route::get('/jurnal', [
    JurnalApiController::class,
    'index'
]);

Route::get('/jurnal-kategori', [
    JurnalApiController::class,
    'kategori'
]);

Route::get('/jurnal/{id}', [
    JurnalApiController::class,
    'show'
]);

Route::get('/classroom', [
    ClassroomController::class,
    'index'
]);

Route::get('/classroom/{id}', [
    ClassroomController::class,
    'show'
]);