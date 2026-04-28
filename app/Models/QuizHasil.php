<?php
// app/Models/QuizHasil.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizHasil extends Model
{
    protected $table = 'quiz_hasil';

    protected $fillable = [
        'session_id', 'user_id', 'score',
        'total_questions', 'correct_count', 'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(QuizSession::class, 'session_id');
    }
}