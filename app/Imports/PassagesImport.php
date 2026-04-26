<?php

namespace App\Imports; // ← tidak ada

use App\Models\Passage; // ← tidak ada
use Maatwebsite\Excel\Concerns\ToModel; // ← tidak ada
use Maatwebsite\Excel\Concerns\WithHeadingRow; // ← tidak ada
use Maatwebsite\Excel\Concerns\WithTitle;

class PassagesImport implements ToModel, WithHeadingRow, WithTitle
{
    public function title(): string
    {
        return 'passages';
    }

    // app/Imports/PassagesImport.php
    public function model(array $row)
    {
        if (empty($row['subject']) || empty($row['content'])) {
            return null;
        }

        return new Passage([
            'id'      => $row['id'],      // ← ambil id dari Excel
            'subject' => $row['subject'],
            'content' => $row['content'],
        ]);
    }
}