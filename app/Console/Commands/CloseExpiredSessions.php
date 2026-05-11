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
        $expired = QuizSession::where('is_active', true)
            ->whereNotNull('ended_at')
            ->where('ended_at', '<=', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Tidak ada sesi yang perlu ditutup.');
            return;
        }

        foreach ($expired as $session) {
            $this->info("Memproses sesi id={$session->id} paket={$session->paket}...");

            // Ambil semua siswa yang sudah mulai tapi belum submit
            $starts = SiswaQuizStart::where('session_id', $session->id)
                ->where('deadline_at', '<=', now())
                ->get();

            foreach ($starts as $start) {
                $userId = $start->user_id;

                // Skip jika sudah submit
                $sudahSubmit = QuizHasil::where('session_id', $session->id)
                    ->where('user_id', $userId)
                    ->exists();

                if ($sudahSubmit) continue;

                // Ambil jawaban yang sudah ada
                $existingAnswers = SiswaAnswer::where('session_id', $session->id)
                    ->where('user_id', $userId)
                    ->get();

                $correctCount = $existingAnswers->where('is_correct', true)->count();
                $earnedPoints = $existingAnswers
                    ->where('is_correct', true)
                    ->sum(fn($a) => Question::find($a->question_id)?->points ?? 1);

                QuizHasil::create([
                    'session_id'      => $session->id,
                    'user_id'         => $userId,
                    'score'           => $earnedPoints,
                    'total_questions' => $existingAnswers->count(),
                    'correct_count'   => $correctCount,
                    'submitted_at'    => $start->deadline_at, // waktu deadline, bukan now()
                ]);

                $this->info("  ✓ User id={$userId} auto-submitted ({$correctCount} benar, {$earnedPoints} poin)");
            }

            // Tutup sesi
            $session->update(['is_active' => false]);
            $this->info("  Sesi id={$session->id} ditutup.");
        }
    }
}
