<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\QuizSession;
use App\Models\QuizHasil;   // ← bukan QuizResult

// app/Http/Controllers/Admin/AdminResultController.php
class AdminResultController extends Controller
{
    public function index()
    {
        $sessions = QuizSession::withCount('results')
                               ->with('creator')
                               ->latest()->paginate(15);

        return view('admin.results.index', compact('sessions'));
    }

    public function show(QuizSession $session)
    {
        $results = QuizHasil::where('session_id', $session->id)
                             ->with('user')
                             ->orderByDesc('score')
                             ->get();

        // Siswa yang belum submit
        $participated = $results->pluck('user_id');
        $notSubmitted = User::where('role', 'siswa')
                            ->when($session->kelas, fn($q) => $q->where('kelas', $session->kelas))
                            ->whereNotIn('id', $participated)
                            ->get();

        return view('admin.results.show', compact('session', 'results', 'notSubmitted'));
    }

    public function detail(QuizSession $session, User $user)
    {
        $answers = StudentAnswer::where('session_id', $session->id)
                                ->where('user_id', $user->id)
                                ->with('question')
                                ->get();

        $result = QuizHasil::where('session_id', $session->id)
                            ->where('user_id', $user->id)
                            ->first();

        return view('admin.results.detail', compact('session', 'user', 'answers', 'result'));
    }
}