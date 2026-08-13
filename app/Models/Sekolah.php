<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'sekolah';
    public $timestamps = false;
    protected $fillable = ['nama', 'jenjang_id', 'kota_kabupaten_id', 'is_active'];

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }

    public function kotaKabupaten()
    {
        return $this->belongsTo(KotaKabupaten::class, 'kota_kabupaten_id');
    }

    public function kelas()
    {
        return $this->hasMany(SekolahKelas::class, 'sekolah_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'sekolah_id');
    }
}