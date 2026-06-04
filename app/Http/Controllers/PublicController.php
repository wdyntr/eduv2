<?php
namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    private array $jenjangData = [
        'sma' => ['nama' => 'SMA', 'icon' => '🎓', 'desc' => 'Materi Sekolah Menengah Atas — Kurikulum Merdeka & K13', 'class' => 'header-sma'],
        'smk' => ['nama' => 'SMK', 'icon' => '🔧', 'desc' => 'Materi Sekolah Menengah Kejuruan — Berbagai Program Keahlian', 'class' => 'header-smk'],
        'slb' => ['nama' => 'SLB', 'icon' => '🌟', 'desc' => 'Materi Sekolah Luar Biasa — Pendidikan Inklusif & Khusus', 'class' => 'header-slb'],
    ];

    public function homepage()
    {
        return view('index', ['active_page' => 'beranda']);
    }

    public function media()
    {
        return view('media', ['active_page' => 'media']);
    }

    public function mediaJenjang(string $jenjang)
    {
        $data = $this->jenjangData[strtolower($jenjang)] ?? null;
        if (!$data) abort(404);

        return view('jenjang', array_merge($data, [
            'active_page' => 'media',
            'jenjang' => strtolower($jenjang),
            'jenjang_nama' => $data['nama'],
            'jenjang_icon' => $data['icon'],
            'jenjang_desc' => $data['desc'],
            'jenjang_class' => $data['class'],
        ]));
    }

    public function mediaDetail(string $jenjang, int $materi_id)
    {
        $materi = Materi::with('mapel')
            ->where('id', $materi_id)
            ->where('is_active', 1)
            ->firstOrFail();

        return view('detail', [
            'active_page' => 'media',
            'materi' => $materi,
            'jenjang' => strtolower($jenjang),
        ]);
    }

    public function classroom()
    {
        return view('classroom', ['active_page' => 'classroom']);
    }
}