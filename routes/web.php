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

// ── IMPORT (opsional, bisa diproteksi nanti) ──
Route::get('/import',         [ImportController::class, 'index']);
Route::post('/import/upload', [ImportController::class, 'import']);

// routes/web.php
Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/quiz',         [QuizController::class, 'dashboard'])->name('quiz.index');
    Route::get('/quiz/mulai',   [QuizController::class, 'index'])->name('quiz.start');
    Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/result',  [QuizController::class, 'result'])->name('quiz.result');
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