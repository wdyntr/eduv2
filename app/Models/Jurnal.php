<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table = 'jurnal';
    protected $fillable = [
        'judul', 'kategori', 'penulis', 'abstrak',
        'jumlah_halaman', 'tahun_terbit', 'volume', 'nomor_edisi', 'issn', 'kata_kunci', 'bahasa',
        'file_jurnal', 'file_bukti_plagiarisme',
        'status', 'catatan_admin', 'admin_id', 'reviewed_by', 'reviewed_at',
    ];

    public function penulisAkun()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    protected static function booted()
    {
        static::deleting(function (Jurnal $jurnal) {
            self::hapusFileFisik($jurnal->file_jurnal);
            self::hapusFileFisik($jurnal->file_bukti_plagiarisme);
        });
    }

    public static function hapusFileFisik(?string $filename): void
    {
        if (!$filename) return;
        $path = config('jurnal.upload_path') . '/' . basename($filename);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
