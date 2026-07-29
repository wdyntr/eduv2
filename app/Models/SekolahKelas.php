<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SekolahKelas extends Model
{
    protected $table = 'sekolah_kelas';
    protected $fillable = ['sekolah_id', 'mapel_id', 'classroom_url'];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function stats()
    {
        return $this->hasMany(ClassroomKelasStats::class, 'sekolah_kelas_id');
    }

    /** Statistik bulan berjalan untuk kelas ini (null kalau belum pernah disinkron) */
    public function statBulanIni()
    {
        return $this->hasOne(ClassroomKelasStats::class, 'sekolah_kelas_id')
            ->where('bulan', now()->format('Y-m'));
    }

    /**
     * Ambil Course ID Google Classroom dari classroom_url yang tersimpan.
     * Format link: https://classroom.google.com/c/{courseId}[/...]
     */
    public function googleCourseId(): ?string
    {
        return self::extractCourseId($this->classroom_url);
    }

    public static function extractCourseId(?string $url): ?string
    {
        if (!$url) return null;
        if (preg_match('#classroom\.google\.com/c/([^/?#]+)#i', $url, $m)) {
            return $m[1];
        }
        return null;
    }
}