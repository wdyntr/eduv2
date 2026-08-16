<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Artikel extends Model
{
    protected $table = 'artikel';
    protected $fillable = ['judul', 'slug', 'kategori_id', 'tipe', 'video_url', 'konten', 'thumbnail', 'is_active'];

    public function kategori()
    {
        return $this->belongsTo(ArtikelKategori::class, 'kategori_id');
    }

    public static function buatSlugUnik(string $judul, ?int $ignoreId = null): string
    {
        $base = Str::slug($judul);
        $slug = $base;
        $i = 1;

        while (
            self::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }

    public static function extractProxyId(?string $thumbnailUrl): ?string
    {
        if ($thumbnailUrl && preg_match('#/thumbnail-proxy/([a-zA-Z0-9_-]+)#', $thumbnailUrl, $m)) {
            return $m[1];
        }
        return null;
    }

    public static function normalizeThumbnailUrl(?string $url): ?string
    {
        if (!$url) return $url;

        $id = null;
        if (preg_match('#drive\.google\.com/file/d/([a-zA-Z0-9_-]+)#', $url, $m)) $id = $m[1];
        elseif (preg_match('#drive\.google\.com/open\?id=([a-zA-Z0-9_-]+)#', $url, $m)) $id = $m[1];
        elseif (preg_match('#drive\.google\.com/thumbnail\?id=([a-zA-Z0-9_-]+)#', $url, $m)) $id = $m[1];

        return $id ? url("/thumbnail-proxy/{$id}") : $url;
    }
}
