<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';
    protected $fillable = ['judul', 'deskripsi', 'tipe', 'mapel_id', 'url', 'thumbnail', 'is_active'];
    // ^ 'jenjang' dihapus dari $fillable, kolom itu sudah tidak ada di tabel

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    /** Jenjang materi sekarang diturunkan dari mapel-nya, bukan kolom sendiri lagi */
    public function getJenjangAttribute()
    {
        return $this->mapel?->jenjang;
    }

    // PENTING: method resolveThumbnail() dan method lain yang sudah ada
    // di file Materi.php kamu SEKARANG biarkan tetap seperti semula,
    // tidak perlu diubah — cuma constructor $fillable + tambahan relasi
    // mapel() dan accessor jenjang di atas yang baru.
}