<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuizSession;
use App\Models\SiswaQuizStart;
use App\Models\SiswaAnswer;
use App\Models\QuizHasil;
use App\Models\Question;

class CloseExpiredSessions extends Command
{
    protected $signature   = 'quiz:close-expired';
    protected $description = 'Nonaktifkan sesi expired dan auto-submit jawaban siswa yang belum submit';

    public function handle()
    {
        // ── 1. Tutup sesi yang sudah expired secara global ──
        $expired = QuizSession::where('is_active', true)
            ->whereNotNull('ended_at')
            ->where('ended_at', '<=', now())
            ->get();

        foreach ($expired as $session) {
            $this->info("Memproses sesi id={$session->id} paket={$session->paket}...");
            $this->autoSubmitForSession($session->id);
            $session->update(['is_active' => false]);
            $this->info("  Sesi id={$session->id} ditutup.");
        }

        // ── 2. Handle deadline personal siswa yang sudah habis
        //       meski sesi globalnya masih aktif ──
        $expiredStarts = SiswaQuizStart::where('deadline_at', '<=', now())
            ->get();

        $handled = 0;
        foreach ($expiredStarts as $start) {
            $sudahSubmit = QuizHasil::where('session_id', $start->session_id)
                ->where('user_id', $start->user_id)
                ->exists();

            if ($sudahSubmit) continue;

            $this->autoSubmitUser($start->session_id, $start->user_id, $start->deadline_at);
            $this->info("  ✓ Personal deadline: user_id={$start->user_id} session_id={$start->session_id} di-submit.");
            $handled++;
        }

        if ($expired->isEmpty() && $handled === 0) {
            $this->info('Tidak ada sesi atau deadline siswa yang perlu diproses.');
        }
    }

    // ── Submit semua siswa dalam satu sesi ──
    private function autoSubmitForSession(int $sessionId): void
    {
        $starts = SiswaQuizStart::where('session_id', $sessionId)
            ->get();

        foreach ($starts as $start) {
            $this->autoSubmitUser($sessionId, $start->user_id, $start->deadline_at);
            $this->info("  ✓ User id={$start->user_id} auto-submitted.");
        }
    }

    // ── Submit satu siswa ──
    private function autoSubmitUser(int $sessionId, int $userId, $submittedAt): void
    {
        if (QuizHasil::where('session_id', $sessionId)->where('user_id', $userId)->exists()) {
            return;
        }

        $existingAnswers = SiswaAnswer::where('session_id', $sessionId)
            ->where('user_id', $userId)
            ->get();

        $correctCount = $existingAnswers->where('is_correct', true)->count();
        $earnedPoints = $existingAnswers
            ->where('is_correct', true)
            ->sum(fn($a) => Question::find($a->question_id)?->points ?? 1);

        QuizHasil::create([
            'session_id'      => $sessionId,
            'user_id'         => $userId,
            'score'           => $earnedPoints,
            'total_questions' => $existingAnswers->count(),
            'correct_count'   => $correctCount,
            'submitted_at'    => $submittedAt,
        ]);
    }
}
