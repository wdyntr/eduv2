<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';
    public $timestamps = false;
    protected $fillable = ['nama', 'jenjang_id'];

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'mapel_id');
    }
}