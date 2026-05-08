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
    public function dashboard()
    {
        $user = Auth::user();

        $activeSessions = QuizSession::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->whereNull('kelas')->orWhere('kelas', $user->kelas);
            })
            ->get();

        $submittedIds = QuizHasil::where('user_id', $user->id)
            ->whereIn('session_id', $activeSessions->pluck('id'))
            ->pluck('session_id')
            ->toArray();

        return view('siswa.dashboard', compact('activeSessions', 'user', 'submittedIds'));
    }

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

        // Cek sudah submit
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
                'deadline_at' => now()->addMinutes($activeSession->durasi),
            ]
        );

        // ── Cek apakah waktu sudah habis ──
        if (now()->gt($start->deadline_at)) {
            // Force submit jawaban yang sudah ada
            $this->forceSubmit($user, $activeSession);
            return redirect()->route('quiz.result', ['session' => $activeSession->id]);
        }

        $questions      = Question::with('passage')
            ->where('paket', $activeSession->paket)
            ->whereHas('passage', fn($q) => $q->where('subject', $activeSession->subject))
            ->orderBy('order')
            ->get();

        $totalQuestions = $questions->count();
        $totalPoints    = $questions->sum('points');

        if ($totalQuestions === 0) {
            return redirect()->route('quiz.index')
                ->with('error', 'Soal untuk sesi ini belum tersedia.');
        }

        // Sisa waktu dalam detik
        $sisaDetik = max(0, (int) now()->diffInSeconds($start->deadline_at));

        return view('quiz.index', compact(
            'questions',
            'totalQuestions',
            'totalPoints',
            'activeSession',
            'sisaDetik',   // ← kirim sisa waktu ke blade
        ));
    }

    public function submit(Request $request)
    {
        $user      = Auth::user();
        $sessionId = $request->input('session_id');

        $activeSession = QuizSession::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->whereNull('kelas')->orWhere('kelas', $user->kelas);
            })
            ->where('id', $sessionId)
            ->first();

        if (!$activeSession) {
            return response()->json(['error' => 'Sesi tidak ditemukan.'], 422);
        }

        // Cegah double submit
        if (QuizHasil::where('session_id', $activeSession->id)->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Anda sudah mengumpulkan jawaban.'], 422);
        }

        // ── Validasi waktu dari server ──
        $start = SiswaQuizStart::where('user_id', $user->id)
            ->where('session_id', $activeSession->id)
            ->first();

        if (!$start) {
            return response()->json(['error' => 'Sesi tidak ditemukan.'], 422);
        }

        // Toleransi 10 detik untuk keterlambatan jaringan
        if (now()->gt($start->deadline_at->addSeconds(10))) {
            // Waktu sudah habis → tetap proses jawaban yang masuk
            // tapi tidak perlu return error, lanjutkan proses
        }

        $answers      = $request->input('answers', []);
        $results      = [];
        $correctCount = 0;
        $earnedPoints = 0;
        $now          = now();

        foreach ($answers as $questionId => $answer) {
            $question  = Question::find($questionId);
            if (!$question) continue;

            $isCorrect = strtoupper($answer) === $question->correct_answer;

            if ($isCorrect) {
                $correctCount++;
                $earnedPoints += $question->points;
            }

            SiswaAnswer::create([
                'session_id'  => $activeSession->id,
                'user_id'     => $user->id,
                'question_id' => $questionId,
                'answer'      => strtoupper($answer),
                'is_correct'  => $isCorrect,
                'answered_at' => $now,
            ]);

            $results[] = [
                'question_id'    => (int) $questionId,
                'student_answer' => strtoupper($answer),
                'correct_answer' => $question->correct_answer,
                'is_correct'     => $isCorrect,
                'feedback'       => $isCorrect ? 'Jawaban kamu benar!' : 'Jawaban kurang tepat.',
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

    // ── Force submit dari server (waktu habis) ──
    private function forceSubmit($user, $activeSession)
    {
        // Cek sudah submit
        if (QuizHasil::where('session_id', $activeSession->id)->where('user_id', $user->id)->exists()) {
            return;
        }

        // Ambil jawaban yang sudah ada di siswa_answers (jika ada)
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
            'submitted_at'    => now(),
        ]);
    }

    public function result(Request $request)
    {
        $user      = Auth::user();
        $sessionId = $request->query('session');

        $hasil = QuizHasil::where('user_id', $user->id)
            ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->with('session')
            ->latest()
            ->firstOrFail();

        // Ambil jawaban siswa beserta soalnya
        $answers = SiswaAnswer::where('session_id', $hasil->session_id)
            ->where('user_id', $user->id)
            ->with('question.passage')
            ->orderBy('question_id')
            ->get()
            ->keyBy('question_id'); // key by question_id untuk lookup mudah

        // Ambil semua soal dari sesi ini
        $questions = Question::with('passage')
            ->where('paket', $hasil->session->paket)
            ->whereHas('passage', fn($q) => $q->where('subject', $hasil->session->subject))
            ->orderBy('order')
            ->get();

        return view('quiz.result', compact('hasil', 'answers', 'questions'));
    }
}
