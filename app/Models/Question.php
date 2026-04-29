<?php
// app/Models/Question.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'passage_id', 'paket', 'passage_highlighted',
        'question_text', 'option_a', 'option_b',
        'option_c', 'option_d', 'option_e',
        'correct_answer', 'points', 'subject_matter', 'order',
    ];

    public function passage()
    {
        return $this->belongsTo(Passage::class);
    }
}