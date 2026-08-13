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