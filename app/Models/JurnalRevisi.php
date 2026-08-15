<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalRevisi extends Model
{
    protected $table = 'jurnal_revisi';
    public $timestamps = false;
    protected $fillable = [
        'jurnal_id', 'versi_ke', 'judul', 'penulis', 'abstrak',
        'jumlah_halaman', 'tahun_terbit', 'volume', 'nomor_edisi', 'issn', 'kata_kunci', 'bahasa',
        'file_jurnal', 'file_bukti_plagiarisme',
    ];

    protected static function booted()
    {
        // Setiap baris revisi dihapus (langsung atau ikut terhapus saat Jurnal dihapus),
        // file fisiknya ikut dibersihkan dari folder uploads.
        static::deleting(function (JurnalRevisi $revisi) {
            self::hapusFileFisik($revisi->file_jurnal);
            self::hapusFileFisik($revisi->file_bukti_plagiarisme);
        });
    }

    public static function hapusFileFisik(?string $filename): void
    {
        if (!$filename) return;
        $path = rtrim(config('jurnal.upload_path'), '/') . '/' . basename($filename);
        if (is_file($path)) @unlink($path);
    }

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'jurnal_id');
    }

    public function review()
    {
        return $this->hasMany(JurnalReview::class, 'jurnal_revisi_id')->orderByDesc('created_at');
    }

    /** Status/keputusan terakhir untuk revisi ini */
    public function reviewTerbaru()
    {
        return $this->hasOne(JurnalReview::class, 'jurnal_revisi_id')->latestOfMany();
    }
}