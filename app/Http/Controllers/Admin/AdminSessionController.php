<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\QuizSession;
use App\Models\Question;
use App\Models\QuizHasil;

class AdminSessionController extends Controller
{
    public function index()
    {
        $sessions = QuizSession::with('creator')
                               ->latest()
                               ->paginate(15);

        // Ambil daftar paket beserta mata pelajaran yang ada di tiap paket
        $pakets = Question::with('passage')
            ->select('paket', 'passage_id')
            ->get()
            ->groupBy('paket')
            ->map(function ($questions, $paket) {
                // Kumpulkan subject unik dari passage; soal tanpa passage dilewati
                $subjects = $questions
                    ->filter(fn($q) => $q->passage)
                    ->pluck('passage.subject')
                    ->unique()
                    ->sort()
                    ->values();

                return [
                    'paket'    => $paket,
                    'subjects' => $subjects,
                    'total'    => $questions->count(),
                ];
            })
            ->sortKeys()
            ->values();

        $kelasList = User::where('role', 'siswa')
                         ->whereNotNull('kelas')
                         ->distinct()
                         ->pluck('kelas')
                         ->sort()
                         ->values();

        return view('admin.sessions.index', compact('sessions', 'pakets', 'kelasList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paket' => 'required|string',
            'kelas' => 'nullable|string',
            // 'durasi' ← hapus dari sini
        ]);

        $exists = Question::where('paket', $data['paket'])->exists();
        if (!$exists) {
            return back()->withErrors(['paket' => 'Paket tidak ditemukan.']);
        }

        $data['created_by'] = auth()->id();
        $data['durasi']     = 240; // ← hardcode 4 jam

        QuizSession::create($data);
        return back()->with('success', 'Sesi ujian dibuat.');
    }

    public function toggle(QuizSession $session)
    {
        if (!$session->is_active) {
            $session->update([
                'is_active'  => true,
                'started_at' => now(),
                'ended_at'   => now()->endOfDay(),
            ]);
        } else {
            // ── Nonaktifkan sesi ──
            $session->update(['is_active' => false, 'ended_at' => now()]);

            // ── Auto-submit semua siswa yang belum submit ──
            $starts = \App\Models\SiswaQuizStart::where('session_id', $session->id)->get();

            foreach ($starts as $start) {
                $sudahSubmit = \App\Models\QuizHasil::where('session_id', $session->id)
                    ->where('user_id', $start->user_id)
                    ->exists();

                if ($sudahSubmit) continue;

                $existingAnswers = \App\Models\SiswaAnswer::where('session_id', $session->id)
                    ->where('user_id', $start->user_id)
                    ->get();

                $correctCount = $existingAnswers->where('is_correct', true)->count();
                $earnedPoints = $existingAnswers->where('is_correct', true)
                    ->sum(fn($a) => \App\Models\Question::find($a->question_id)?->points ?? 1);

                $totalInPaket = \App\Models\Question::where('paket', $session->paket)->count();

                \App\Models\QuizHasil::create([
                    'session_id'      => $session->id,
                    'user_id'         => $start->user_id,
                    'score'           => $earnedPoints,
                    'total_questions' => $totalInPaket,
                    'correct_count'   => $correctCount,
                    'submitted_at'    => now(),
                ]);
            }
        }

        return back()->with('success', 'Status sesi diperbarui.');
    }

    public function destroy(QuizSession $session)
    {
        $session->results()->delete();
        $session->answers()->delete();
        $session->starts()->delete();
        $session->delete();

        return back()->with('success', 'Sesi dihapus.');
    }
}
