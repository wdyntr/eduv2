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

    /** Ambil thumbnail otomatis. Video YouTube: pakai thumbnail bawaan YouTube. Selain itu: null. */
    public static function resolveThumbnail(string $tipe, ?string $url): ?string
    {
        if ($tipe !== 'video' || !$url) {
            return null;
        }

        preg_match(
            '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/',
            $url,
            $match
        );

        return isset($match[1])
            ? "https://img.youtube.com/vi/{$match[1]}/hqdefault.jpg"
            : null;
    }
}