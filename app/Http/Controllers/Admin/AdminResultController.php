<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\QuizSession;
use App\Models\QuizHasil;
use App\Models\SiswaAnswer; // ← ini yang kurang

class AdminResultController extends Controller
{
    public function index()
    {
        $sessions = QuizSession::withCount('results')  // ← sesuai nama relasi di model
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

        $participated = $results->pluck('user_id');
        $notSubmitted = User::where('role', 'siswa')
                            ->when($session->kelas, fn($q) => $q->where('kelas', $session->kelas))
                            ->whereNotIn('id', $participated)
                            ->get();

        return view('admin.results.show', compact('session', 'results', 'notSubmitted'));
    }

    public function detail(QuizSession $session, User $user)
    {
        $answers = SiswaAnswer::where('session_id', $session->id)  // ← SiswaAnswer, bukan StudentAnswer
                              ->where('user_id', $user->id)
                              ->with('question')
                              ->orderBy('question_id')
                              ->get();

        $result = QuizHasil::where('session_id', $session->id)
                            ->where('user_id', $user->id)
                            ->first();

        return view('admin.results.detail', compact('session', 'user', 'answers', 'result'));
    }
}