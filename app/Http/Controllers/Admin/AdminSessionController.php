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
            // ← HAPUS blok nonaktifkan sesi lain
            $session->update([
                'is_active'  => true,
                'started_at' => now(),
                'ended_at'   => null,
            ]);
        } else {
            $session->update(['is_active' => false, 'ended_at' => now()]);
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
