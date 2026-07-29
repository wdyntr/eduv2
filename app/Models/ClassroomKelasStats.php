<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomKelasStats extends Model
{
    protected $table = 'classroom_kelas_stats';
    public $timestamps = false;
    protected $fillable = [
        'sekolah_kelas_id', 'bulan', 'jumlah_guru', 'jumlah_siswa',
        'jumlah_task', 'jumlah_materi', 'synced_at',
    ];

    public function sekolahKelas()
    {
        return $this->belongsTo(SekolahKelas::class, 'sekolah_kelas_id');
    }
}