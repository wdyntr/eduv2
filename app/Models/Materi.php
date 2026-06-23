<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';
    protected $fillable = ['judul', 'deskripsi', 'tipe', 'jenjang', 'mapel_id', 'url', 'thumbnail'];

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    /**
     * Ekstrak video ID dari berbagai format URL YouTube.
     * Mendukung: watch?v=, youtu.be/, embed/, shorts/, ?v= dengan parameter tambahan.
     */
    public static function extractYoutubeId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([^"&?\/\s]{11})/i';

        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Bangun URL thumbnail YouTube dari video ID.
     */
    public static function youtubeThumbnailUrl(string $videoId, string $quality = 'hqdefault'): string
    {
        return "https://img.youtube.com/vi/{$videoId}/{$quality}.jpg";
    }

    /**
     * Tentukan thumbnail otomatis berdasarkan tipe & url.
     * Mengembalikan null jika bukan video atau ID tidak ditemukan (supaya fallback emoji tetap jalan).
     */
    public static function resolveThumbnail(string $tipe, ?string $url): ?string
    {
        if ($tipe !== 'video') {
            return null;
        }

        $videoId = self::extractYoutubeId($url);
        return $videoId ? self::youtubeThumbnailUrl($videoId) : null;
    }
}
