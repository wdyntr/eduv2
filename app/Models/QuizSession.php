<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSession extends Model
{
    protected $fillable = [
        'paket', 'kelas', 'durasi',
        'started_at', 'ended_at',
        'created_by', 'is_active',
    ];

    // app/Models/QuizSession.php
    protected $casts = [
        'durasi'     => 'integer',
        'is_active'  => 'boolean',
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function results()
    {
        return $this->hasMany(QuizHasil::class, 'session_id');
    }

    public function answers()
    {
        return $this->hasMany(SiswaAnswer::class, 'session_id');
    }

    public function starts()
    {
        return $this->hasMany(SiswaQuizStart::class, 'session_id');
    }

    // ── Helper ──────────────────────────────────────────────

    /**
     * Label mata pelajaran yang ada dalam paket ini.
     * Dipakai di tabel sesi agar tidak ada kolom subject lagi.
     */
    public function subjectLabel(): string
    {
        $subjects = Question::with('passage')
            ->where('paket', $this->paket)
            ->get()
            ->filter(fn($q) => $q->passage)
            ->pluck('passage.subject')
            ->unique()
            ->map(fn($s) => str_replace('_', ' ', ucwords($s, '_')))
            ->sort()
            ->values();

        return $subjects->isNotEmpty()
            ? $subjects->implode(', ')
            : 'Semua Mapel';
    }

    /**
     * Soal-soal yang termasuk dalam sesi ini (semua mapel, filter paket saja).
     */
    public function questions()
    {
        return Question::where('paket', $this->paket)
                       ->orderBy('order')
                       ->get();
    }
}
