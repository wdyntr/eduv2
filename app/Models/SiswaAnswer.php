<?php
// app/Models/SiswaAnswer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaAnswer extends Model
{
    protected $table = 'siswa_answers';

    protected $fillable = [
        'session_id', 'user_id', 'question_id', 'answer', 'is_correct', 'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'is_correct'  => 'boolean',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function session()  { return $this->belongsTo(QuizSession::class, 'session_id'); }
    public function question() { return $this->belongsTo(Question::class); }
}