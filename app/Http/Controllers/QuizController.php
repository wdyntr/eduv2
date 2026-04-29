<?php
// app/Http/Controllers/QuizController.php
namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuizSession;
use App\Models\QuizHasil;
use App\Models\SiswaAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    // ── Dashboard siswa (route: quiz.index = GET /quiz) ──
    public function dashboard()
    {
        $user = Auth::user();

        $activeSession = QuizSession::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->whereNull('kelas')->orWhere('kelas', $user->kelas);
            })
            ->first();

        return view('siswa.dashboard', compact('activeSession', 'user'));
    }

    // ── Halaman soal (route: quiz.start = GET /quiz/mulai) ──
    public function index()
    {
        $user = Auth::user();

        $activeSession = QuizSession::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->whereNull('kelas')->orWhere('kelas', $user->kelas);
            })
            ->first();

        if (!$activeSession) {
            return redirect()->route('quiz.index')
                            ->with('error', 'Tidak ada sesi ujian yang aktif.');
        }

        $sudahSubmit = QuizHasil::where('session_id', $activeSession->id)
                                ->where('user_id', $user->id)
                                ->exists();

        if ($sudahSubmit) {
            return redirect()->route('quiz.result', ['session' => $activeSession->id]);
        }

        $questions = Question::with('passage')
            ->where('paket', $activeSession->paket)
            ->whereHas('passage', fn($q) => $q->where('subject', $activeSession->subject))
            ->orderBy('order')
            ->get();

        $groups         = $questions->groupBy('passage_id');
        $totalQuestions = $questions->count();
        $totalPoints    = $questions->sum('points');

        if ($totalQuestions === 0) {
            return redirect()->route('quiz.index')
                            ->with('error', 'Soal untuk sesi ini belum tersedia.');
        }

        return view('quiz.index', compact(
            'questions',        // ← tambahkan ini
            'groups',
            'totalQuestions',
            'totalPoints',
            'activeSession',
        ));
    }

    // ── Submit jawaban ──
    public function submit(Request $request)
    {
        $user = Auth::user();

        $activeSession = QuizSession::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->whereNull('kelas')->orWhere('kelas', $user->kelas);
            })
            ->first();

        if (!$activeSession) {
            return response()->json(['error' => 'Sesi tidak ditemukan.'], 422);
        }

        // Cegah double submit
        $sudahSubmit = QuizHasil::where('session_id', $activeSession->id)
                                ->where('user_id', $user->id)
                                ->exists();

        if ($sudahSubmit) {
            return response()->json(['error' => 'Anda sudah mengumpulkan jawaban.'], 422);
        }

        $answers      = $request->input('answers'); // ['questionId' => 'A', ...]
        $results      = [];
        $correctCount = 0;
        $totalPoints  = 0;
        $earnedPoints = 0;
        $now          = now();

        foreach ($answers as $questionId => $answer) {
            $question  = Question::findOrFail($questionId);
            $isCorrect = strtoupper($answer) === $question->correct_answer;

            if ($isCorrect) {
                $correctCount++;
                $earnedPoints += $question->points;
            }
            $totalPoints += $question->points;

            // Simpan ke siswa_answers
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
                'feedback'       => $isCorrect
                    ? 'Jawaban kamu benar!'
                    : 'Jawaban kurang tepat.',
            ];
        }

        // Simpan rekap ke quiz_hasil
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

    // ── Halaman hasil ──
    public function result(Request $request)
    {
        $user      = Auth::user();
        $sessionId = $request->query('session');

        $hasil = QuizHasil::where('user_id', $user->id)
            ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->with('session')
            ->latest()
            ->firstOrFail();

        return view('quiz.result', compact('hasil'));
    }
}