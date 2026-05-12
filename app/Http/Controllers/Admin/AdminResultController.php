<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\QuizSession;
use App\Models\QuizHasil;
use App\Models\Question;
use App\Models\SiswaAnswer; // ← ini yang kurang

class AdminResultController extends Controller
{
    public function index()
    {
        $sessions = QuizSession::withCount('results')
                            ->with('creator')
                            ->latest()->paginate(15);

        // Tambah label subject per sesi
        $sessions->each(function ($session) {
            $session->subject_label = \App\Models\Question::with('passage')
                ->where('paket', $session->paket)
                ->get()
                ->filter(fn($q) => $q->passage)
                ->pluck('passage.subject')
                ->unique()
                ->map(fn($s) => ucwords(str_replace('_', ' ', $s)))
                ->sort()->implode(', ');
        });

        return view('admin.results.index', compact('sessions'));
    }


    public function show(QuizSession $session)
    {
        // ❌ quizhasil → QuizHasil
        $results = QuizHasil::where('session_id', $session->id)
                            ->with('user')
                            ->orderByDesc('score')
                            ->get();

        // ❌ App\Models\Question (tanpa backslash di depan) → pakai import di atas
        $totalQuestions = Question::where('paket', $session->paket)->count();

        $participated = $results->pluck('user_id');

        // ❌ user:: → User::, wherenotin → whereNotIn
        $notSubmitted = User::where('role', 'siswa')
                            ->when($session->kelas, fn($q) => $q->where('kelas', $session->kelas))
                            ->whereNotIn('id', $participated)
                            ->get();

        // ❌ \app\models\question → pakai Question yang sudah diimport
        $subjectLabel = Question::with('passage')
            ->where('paket', $session->paket)
            ->get()
            ->filter(fn($q) => $q->passage)
            ->pluck('passage.subject')
            ->unique()
            ->map(fn($s) => ucwords(str_replace('_', ' ', $s)))
            ->sort()->implode(', ');

        return view('admin.results.show', compact(
            'session', 'results', 'notSubmitted', 'subjectLabel', 'totalQuestions'
            // ❌ nama variabel di compact harus sama persis dengan nama variabel di atas
            // notsubmitted → notSubmitted, subjectlabel → subjectLabel, totalquestions → totalQuestions
        ));
    }


    public function detail(QuizSession $session, User $user)
    {
        // ✅ Ambil semua soal dari paket, bukan dari jawaban siswa
        $questions = \App\Models\Question::with('passage')
            ->where('paket', $session->paket)
            ->orderedBySubject()
            ->get();

        $answers = SiswaAnswer::where('session_id', $session->id)
                            ->where('user_id', $user->id)
                            ->get()
                            ->keyBy('question_id'); // ← keyBy untuk lookup cepat

        $result = QuizHasil::where('session_id', $session->id)
                            ->where('user_id', $user->id)
                            ->first();

        return view('admin.results.detail', compact(
            'session', 'user', 'answers', 'result', 'questions'
        ));
    }

}
