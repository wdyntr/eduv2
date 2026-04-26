<?php

namespace App\Imports; // ← tidak ada

use App\Models\Question; // ← tidak ada
use Maatwebsite\Excel\Concerns\ToModel; // ← tidak ada
use Maatwebsite\Excel\Concerns\WithHeadingRow; // ← tidak ada
use Maatwebsite\Excel\Concerns\WithTitle;

class QuestionsImport implements ToModel, WithHeadingRow, WithTitle
{
    public function title(): string
    {
        return 'questions';
    }

    public function model(array $row)
    {
        if (empty($row['question_text'])) {
            return null;
        }

        $highlighted = $row['passage_highlighted'] ?? null;
        if (empty($highlighted) || strtolower(trim($highlighted)) === '(kosong)') {
            $highlighted = null;
        }

        return new Question([
            'passage_id'          => $row['passage_id'] ?? null,
            'paket'               => $row['paket'] ?? null,
            'passage_highlighted' => $highlighted,
            'question_text'       => $row['question_text'],
            'option_a'            => $row['option_a'],
            'option_b'            => $row['option_b'],
            'option_c'            => $row['option_c'],
            'option_d'            => $row['option_d'],
            'option_e'            => $row['option_e'] ?? null,
            'correct_answer'      => strtoupper($row['correct_answer']),
            'points'              => $row['points'] ?? 1,
            'subject_matter'      => $row['subject_matter'] ?? null,
            'order'               => $row['order'] ?? 0,
        ]);
    }
}