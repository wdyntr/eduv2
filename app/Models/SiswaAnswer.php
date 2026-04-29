<?php
// app/Models/SiswaAnswer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaAnswer extends Model
{
    protected $table = 'siswa_answers';

    protected $fillable = [
        'session_id', 'user_id', 'question_id',
        'answer', 'is_correct', 'answered_at',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}