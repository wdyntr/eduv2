<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\QuizController;


Route::get('/', function () {
    // return view('welcome');
    return view('quiz.quiz');
});

// routes/web.php
Route::get('/import',         [ImportController::class, 'index']);
Route::post('/import/upload', [ImportController::class, 'import']);

// routes/web.php
Route::get('/quiz',          [QuizController::class, 'index'])->name('quiz.index');
Route::post('/quiz/submit',  [QuizController::class, 'submit'])->name('quiz.submit');
Route::get('/quiz/result',   [QuizController::class, 'result'])->name('quiz.result');