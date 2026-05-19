<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AdminResultController;

// ── AUTH ──────────────────────────────────────
Route::get('/',       [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// Untuk refresh CSRF token
Route::get('/logout-token', function () {
    return response()->json(['token' => csrf_token()]);
})->middleware('auth');

// Fallback logout via GET
Route::get('/logout', [AuthController::class, 'logout'])
     ->name('logout.get')
     ->middleware('auth');

// ── IMPORT (opsional, bisa diproteksi nanti) ──
Route::get('/import',         [ImportController::class, 'index']);
Route::post('/import/upload', [ImportController::class, 'import']);

// routes/web.php
Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/quiz',         [QuizController::class, 'dashboard'])->name('quiz.index');
    Route::get('/quiz/mulai',   [QuizController::class, 'index'])->name('quiz.start');
    Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/result',  [QuizController::class, 'result'])->name('quiz.result');
    Route::post('/quiz/save-answers', [QuizController::class, 'saveAnswersBulk'])->name('quiz.save-answers'); // ← tambah ini

});

// ── ADMIN ─────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::patch('users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])
         ->name('users.toggle-active');

    Route::resource('sessions', AdminSessionController::class)->except(['show']);
    Route::patch('sessions/{session}/toggle', [AdminSessionController::class, 'toggle'])
         ->name('sessions.toggle');

    Route::get('results',                         [AdminResultController::class, 'index'])->name('results.index');
    Route::get('results/{session}',               [AdminResultController::class, 'show'])->name('results.show');
    Route::get('results/{session}/user/{user}',   [AdminResultController::class, 'detail'])->name('results.detail');
});


// ── FALLBACK ──────────────────────────────────
Route::fallback(function () {
    if (auth()->check()) {
        $role = auth()->user()->role;

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('quiz.index');
    }

    return redirect()->route('login');
});
