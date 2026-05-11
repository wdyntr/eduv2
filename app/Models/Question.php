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

    // app/Models/Question.php

    public function scopeOrderedBySubject($query)
    {
        return $query
            ->leftJoin('passages', 'passages.id', '=', 'questions.passage_id')
            ->orderByRaw("
                CASE passages.subject
                    WHEN 'bahasa_indonesia' THEN 1
                    WHEN 'bahasa_inggris'   THEN 2
                    WHEN 'matematika'       THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('questions.order')
            ->select('questions.*');
    }
}
