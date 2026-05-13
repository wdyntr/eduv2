<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuizSession;
use App\Models\QuizHasil;
use App\Models\SiswaAnswer;
use App\Models\SiswaQuizStart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    private const SUBJECT_ORDER = [
        'bahasa_indonesia' => 1,
        'bahasa_inggris'   => 2,
        'matematika'       => 3,
    ];

    // ══════════════════════════════════════
    //  DASHBOARD SISWA
    // ══════════════════════════════════════
    public function dashboard()
    {
        $user = Auth::user();

        // ── Fallback: auto-submit jika ada deadline personal yang sudah habis
        //    tapi quiz_hasil belum ada (misal siswa logout sebelum waktu habis) ──
        $expiredStarts = SiswaQuizStart::where('user_id', $user->id)
            ->where('deadline_at', '<=', now())
            ->get();

        if ($expiredStarts->isNotEmpty()) {
            $sessionIds    = $expiredStarts->pluck('session_id');
            $sudahSubmitIds = QuizHasil::where('user_id', $user->id)
                ->whereIn('session_id', $sessionIds)
                ->pluck('session_id')
                ->toArray();

            // Pre-load semua questions sekali saja
            $sessions = QuizSession::whereIn('id', $sessionIds)->get()->keyBy('id');

            foreach ($expiredStarts as $start) {
                if (in_array($start->session_id, $sudahSubmitIds)) continue;

                $existingAnswers = SiswaAnswer::where('session_id', $start->session_id)
                    ->where('user_id', $user->id)
                    ->get();

                $questionIds = $existingAnswers->pluck('question_id');
                $pointsMap   = Question::whereIn('id', $questionIds)->pluck('points', 'id');

                $correctCount = $existingAnswers->where('is_correct', true)->count();
                $earnedPoints = $existingAnswers
                    ->where('is_correct', true)
                    ->sum(fn($a) => $pointsMap[$a->question_id] ?? 1);

                QuizHasil::create([
                    'session_id'      => $start->session_id,
                    'user_id'         => $user->id,
                    'score'           => $earnedPoints,
                    'total_questions' => $existingAnswers->count(),
                    'correct_count'   => $correctCount,
                    'submitted_at'    => $start->deadline_at,
                ]);
            }
        }

        // ── Ambil sesi aktif ──
        $activeSessions = QuizSession::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->whereNull('kelas')->orWhere('kelas', $user->kelas);
            })
            ->get();

        $submittedIds = QuizHasil::where('user_id', $user->id)
            ->whereIn('session_id', $activeSessions->pluck('id'))
            ->pluck('session_id')
            ->toArray();

        $sessionSubjects = $activeSessions->mapWithKeys(function ($session) {
            $subjects = \App\Models\Question::with('passage')
                ->where('paket', $session->paket)
                ->get()
                ->filter(fn($q) => $q->passage)
                ->pluck('passage.subject')
                ->unique()
                ->map(fn($s) => str_replace('_', ' ', ucwords($s, '_')))
                ->sort()
                ->values();

            return [$session->id => $subjects->implode(', ') ?: 'Semua Mapel'];
        });

        return view('siswa.dashboard', compact('activeSessions', 'user', 'submittedIds', 'sessionSubjects'));
    }

    // ══════════════════════════════════════
    //  HALAMAN QUIZ
    // ══════════════════════════════════════
    public function index(Request $request)
    {
        $user      = Auth::user();
        $sessionId = $request->query('session');

        $query = QuizSession::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->whereNull('kelas')->orWhere('kelas', $user->kelas);
            });

        $activeSession = $sessionId
            ? $query->where('id', $sessionId)->firstOrFail()
            : $query->firstOrFail();


        // ── Cek sudah submit ──
        $sudahSubmit = QuizHasil::where('session_id', $activeSession->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($sudahSubmit) {
            return redirect()->route('quiz.result', ['session' => $activeSession->id]);
        }

        // ── Cek/buat record start siswa ──
        $start = SiswaQuizStart::firstOrCreate(
            ['user_id' => $user->id, 'session_id' => $activeSession->id],
            [
                'started_at'  => now(),
                'deadline_at' => now()->addMinutes($activeSession->durasi), // ← per siswa
            ]
        );

        // ── Cek apakah waktu siswa sudah habis ──
        if (now()->gt($start->deadline_at)) {
            $this->forceSubmit($user, $activeSession, $start->deadline_at);
            return redirect()->route('quiz.result', ['session' => $activeSession->id]);
        }

        $questions      = Question::with('passage')
            ->where('paket', $activeSession->paket)
            ->orderedBySubject()
            ->get();

        $totalQuestions = $questions->count();
        $totalPoints    = $questions->sum('points');

        if ($totalQuestions === 0) {
            return redirect()->route('quiz.dashboard')
                ->with('error', 'Soal untuk sesi ini belum tersedia.');
        }

        $sisaDetik = max(0, (int) now()->diffInSeconds($start->deadline_at));

        return view('quiz.index', compact(
            'questions',
            'totalQuestions',
            'totalPoints',
            'activeSession',
            'sisaDetik',
        ));
    }

    // ══════════════════════════════════════
    //  SAVE ANSWERS BULK (periodic sync)
    // ══════════════════════════════════════
    public function saveAnswersBulk(Request $request)
    {
        $user      = Auth::user();
        $sessionId = $request->input('session_id');
        $answers   = $request->input('answers', []);

        // Jika dikirim sebagai string JSON (dari sendBeacon FormData)
        if (is_string($answers)) {
            $answers = json_decode($answers, true) ?? [];
        }

        if (empty($answers)) {
            return response()->json(['ok' => true, 'saved' => 0]);
        }

        $session = QuizSession::where('id', $sessionId)
            ->where(function ($q) use ($user) {
                $q->whereNull('kelas')->orWhere('kelas', $user->kelas);
            })->first();

        if (!$session) {
            return response()->json(['ok' => false, 'error' => 'Sesi tidak ditemukan.']);
        }

        // Cek deadline siswa
        $start = SiswaQuizStart::where('user_id', $user->id)
            ->where('session_id', $session->id)
            ->first();

        if (!$start) {
            return response()->json(['ok' => false, 'error' => 'Data start tidak ditemukan.']);
        }

        // ← kembalikan ke $start->deadline_at (bukan $session->ended_at)
        if (now()->gt($start->deadline_at->addSeconds(30))) {
            return response()->json(['ok' => false, 'error' => 'Waktu telah habis.']);
        }

        // Sudah submit? Tidak perlu sync lagi
        if (QuizHasil::where('session_id', $session->id)->where('user_id', $user->id)->exists()) {
            return response()->json(['ok' => true, 'already_submitted' => true]);
        }

        $saved = 0;
        foreach ($answers as $questionId => $answer) {
            $question = Question::find($questionId);
            if (!$question) continue;

            $answer = strtoupper($answer);
            if (!in_array($answer, ['A', 'B', 'C', 'D', 'E'])) continue;

            SiswaAnswer::updateOrCreate(
                [
                    'session_id'  => $session->id,
                    'user_id'     => $user->id,
                    'question_id' => $questionId,
                ],
                [
                    'answer'      => $answer,
                    'is_correct'  => $answer === $question->correct_answer,
                    'answered_at' => now(),
                ]
            );
            $saved++;
        }

        return response()->json(['ok' => true, 'saved' => $saved]);
    }

    // ══════════════════════════════════════
    //  SUBMIT FINAL
    // ══════════════════════════════════════
    public function submit(Request $request)
    {
        $user      = Auth::user();
        $sessionId = $request->input('session_id');

        $activeSession = QuizSession::where('id', $sessionId)
            ->where(function ($q) use ($user) {
                $q->whereNull('kelas')->orWhere('kelas', $user->kelas);
            })->first();

        if (!$activeSession) {
            return response()->json(['error' => 'Sesi tidak ditemukan.'], 422);
        }

        // Double submit — kembalikan ok agar JS redirect ke result
        if (QuizHasil::where('session_id', $activeSession->id)
                      ->where('user_id', $user->id)->exists()) {
            return response()->json(['already' => true]);
        }

        $start = SiswaQuizStart::where('user_id', $user->id)
            ->where('session_id', $activeSession->id)
            ->first();

        if (!$start) {
            return response()->json(['error' => 'Data sesi tidak ditemukan.'], 422);
        }

        $answers      = $request->input('answers', []);
        $correctCount = 0;
        $earnedPoints = 0;
        $results      = [];
        $now          = now();

        foreach ($answers as $questionId => $answer) {
            $question = Question::find($questionId);
            if (!$question) continue;

            $answer    = strtoupper($answer);
            $isCorrect = $answer === $question->correct_answer;

            if ($isCorrect) {
                $correctCount++;
                $earnedPoints += $question->points;
            }

            SiswaAnswer::updateOrCreate(
                [
                    'session_id'  => $activeSession->id,
                    'user_id'     => $user->id,
                    'question_id' => $questionId,
                ],
                [
                    'answer'      => $answer,
                    'is_correct'  => $isCorrect,
                    'answered_at' => $now,
                ]
            );

            $results[] = [
                'question_id'    => (int) $questionId,
                'student_answer' => $answer,
                'correct_answer' => $question->correct_answer,
                'is_correct'     => $isCorrect,
            ];
        }

        QuizHasil::create([
            'session_id'      => $activeSession->id,
            'user_id'         => $user->id,
            'score'           => $earnedPoints,
            'total_questions' => count($answers),
            'correct_count'   => $correctCount,
            'submitted_at'    => $now,
        ]);

        return response()->json([
            'results' => $results,
            'correct' => $correctCount,
            'total'   => count($answers),
            'score'   => $earnedPoints,
        ]);
    }

    // ══════════════════════════════════════
    //  FORCE SUBMIT (dari server)
    // ══════════════════════════════════════
    private function forceSubmit($user, $activeSession, $submittedAt = null): void
    {
        if (QuizHasil::where('session_id', $activeSession->id)
                      ->where('user_id', $user->id)->exists()) {
            return;
        }

        $existingAnswers = SiswaAnswer::where('session_id', $activeSession->id)
            ->where('user_id', $user->id)
            ->get();

        $correctCount = $existingAnswers->where('is_correct', true)->count();
        $earnedPoints = $existingAnswers->where('is_correct', true)
            ->sum(fn($a) => Question::find($a->question_id)?->points ?? 0);

        QuizHasil::create([
            'session_id'      => $activeSession->id,
            'user_id'         => $user->id,
            'score'           => $earnedPoints,
            'total_questions' => $existingAnswers->count(),
            'correct_count'   => $correctCount,
            'submitted_at'    => $submittedAt ?? now(),
        ]);
    }

    // ══════════════════════════════════════
    //  HASIL
    // ══════════════════════════════════════
    public function result(Request $request)
    {
        $user      = Auth::user();
        $sessionId = $request->query('session');

        $hasil = QuizHasil::where('user_id', $user->id)
            ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->with('session')
            ->latest()
            ->first();
        // Jika belum ada hasil, coba force submit dulu
        if (!$hasil) {
            $session = QuizSession::find($sessionId);
            if ($session) {
                $start = SiswaQuizStart::where('user_id', $user->id)
                    ->where('session_id', $sessionId)
                    ->first();

                if ($start) {
                    $this->forceSubmit($user, $session, now());

                    // Ambil lagi setelah force submit
                    $hasil = QuizHasil::where('user_id', $user->id)
                        ->where('session_id', $sessionId)
                        ->with('session')
                        ->first();
                }
            }

            // Jika masih tidak ada → kembali ke dashboard
            if (!$hasil) {
                return redirect()->route('quiz.index')
                    ->with('error', 'Data hasil tidak ditemukan.');
            }
        }

        $answers = SiswaAnswer::where('session_id', $hasil->session_id)
            ->where('user_id', $user->id)
            ->with('question.passage')
            ->orderBy('question_id')
            ->get()
            ->keyBy('question_id');

        $questions = Question::with('passage')
            ->where('paket', $hasil->session->paket)
            ->orderedBySubject()
            ->get();

        $subjectOrder = ['bahasa_indonesia', 'bahasa_inggris', 'matematika'];

        $subjectBreakdown = $answers
            ->groupBy(fn($a) => $a->question->passage?->subject ?? 'lainnya')
            ->map(fn($group, $subject) => [
                'label'   => ucwords(str_replace('_', ' ', $subject)),
                'correct' => $group->where('is_correct', true)->count(),
                'total'   => $group->count(),
                'points'  => $group->where('is_correct', true)
                                   ->sum(fn($a) => $a->question->points ?? 1),
            ])
            ->sortBy(fn($v, $k) => array_search($k, $subjectOrder))
            ->values();

        return view('quiz.result', compact('hasil', 'answers', 'questions', 'subjectBreakdown'));
    }
}
