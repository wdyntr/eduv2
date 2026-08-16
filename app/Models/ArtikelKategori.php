<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArtikelKategori extends Model
{
    protected $table = 'artikel_kategori';
    public $timestamps = false;
    protected $fillable = ['nama', 'slug'];

    public function artikel()
    {
        return $this->hasMany(Artikel::class, 'kategori_id');
    }

    public static function buatSlugUnik(string $nama, ?int $ignoreId = null): string
    {
        $base = Str::slug($nama);
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
}