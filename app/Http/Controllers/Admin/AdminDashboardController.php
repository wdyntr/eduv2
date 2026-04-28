<?php
// app/Http/Controllers/Admin/AdminDashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\QuizSession;
use App\Models\QuizHasil;   // ← bukan QuizResult

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_siswa' => User::where('role', 'siswa')->count(),
            'total_admin' => User::where('role', 'admin')->count(),
            'sesi_aktif'  => QuizSession::where('is_active', true)->count(),
            'total_hasil' => QuizHasil::count(),
        ];

        $recentSessions = QuizSession::with('creator')
                                     ->latest()->limit(5)->get();

        $recentResults  = QuizHasil::with(['user', 'session'])
                                   ->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentSessions', 'recentResults'));
    }
}