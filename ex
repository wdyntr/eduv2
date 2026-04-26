// app/Imports/PassagesImport.php
namespace App\Imports;

use App\Models\Passage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PassagesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Passage([
            'id'      => $row['id'],          // penting agar passage_id di questions cocok
            'subject' => $row['subject'],
            'content' => $row['content'],
            'title'   => $row['title'] ?? null,
        ]);
    }
}
