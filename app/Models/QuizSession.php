<?php
// app/Models/QuizSession.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSession extends Model
{
    protected $table = 'quiz_sessions';

    protected $fillable = [
        'paket', 'subject', 'kelas',
        'durasi',           // ← sesuai nama kolom di DB
        'started_at', 'ended_at',
        'created_by', 'is_active',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function results()
    {
        return $this->hasMany(QuizHasil::class, 'session_id');
    }

    // app/Models/QuizSession.php
    public function answers()
    {
        return $this->hasMany(SiswaAnswer::class, 'session_id');
    }
}
