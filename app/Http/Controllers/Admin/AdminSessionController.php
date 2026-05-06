<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\QuizSession;
use App\Models\Question;
use App\Models\QuizHasil;   // ← bukan QuizResult

// app/Http/Controllers/Admin/AdminSessionController.php
class AdminSessionController extends Controller
{
    public function index()
    {
        $sessions = QuizSession::with('creator')
                               ->latest()->paginate(15);

        // Ambil daftar paket yang tersedia dari tabel questions
        $pakets = Question::distinct()->pluck('paket')->sort();

        $kelasList = User::where('role', 'siswa')
                         ->whereNotNull('kelas')
                         ->distinct()->pluck('kelas')->sort();

        return view('admin.sessions.index', compact('sessions', 'pakets', 'kelasList'));
    }

    public function store(Request $request)
    {
        // AdminSessionController@store — ganti duration_minutes → durasi
        $data = $request->validate([
            'paket'   => 'required|string',
            'subject' => 'required|in:matematika,bahasa_inggris,bahasa_indonesia',
            'kelas'   => 'nullable|string',
            'durasi'  => 'required|integer|min:5|max:300',  // ← sesuai kolom DB
        ]);

        $data['created_by'] = auth()->id();

        QuizSession::create($data);
        return back()->with('success', 'Sesi ujian dibuat.');
    }

    public function toggle(QuizSession $session)
    {
        // Nonaktifkan semua sesi lain dulu untuk kelas yang sama
        if (!$session->is_active) {
            QuizSession::where('kelas', $session->kelas)
                       ->where('is_active', true)
                       ->update(['is_active' => false, 'ended_at' => now()]);

            $session->update([
                'is_active'  => true,
                'started_at' => now(),
                'ended_at'   => now()->addMinutes($session->duration_minutes),
            ]);
        } else {
            $session->update(['is_active' => false, 'ended_at' => now()]);
        }

        return back()->with('success', 'Status sesi diperbarui.');
    }

   public function destroy(QuizSession $session)
    {
        // Hapus data terkait sebelum menghapus sesi
        $session->results()->delete();   // quiz_hasil
        $session->answers()->delete();   // siswa_answers

        // siswa_quiz_starts (tambahkan relasi jika belum ada)
        $session->starts()->delete();

        $session->delete();
        return back()->with('success', 'Sesi dihapus.');
   }
}
