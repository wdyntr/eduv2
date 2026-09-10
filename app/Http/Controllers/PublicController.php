<?php
namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Artikel;
use App\Models\ArtikelKategori;
use App\Models\Ptn;
use App\Models\PtnProdi;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    private array $jenjangData = [
        'sma' => ['nama' => 'SMA', 'icon' => '🎓', 'desc' => 'Materi Sekolah Menengah Atas — Kurikulum Merdeka & K13', 'class' => 'header-sma'],
        'smk' => ['nama' => 'SMK', 'icon' => '🔧', 'desc' => 'Materi Sekolah Menengah Kejuruan — Berbagai Program Keahlian', 'class' => 'header-smk'],
        'slb' => ['nama' => 'SLB', 'icon' => '🌟', 'desc' => 'Materi Sekolah Luar Biasa — Pendidikan Inklusif & Khusus', 'class' => 'header-slb'],
    ];

    private array $mapelRapor = [
        'Matematika',
        'Bahasa Indonesia',
        'Bahasa Inggris',
        'Pendidikan Pancasila',
        'Sejarah',
        'Agama',
        'PJOK',
        'Seni Budaya',
        'Biologi',
        'Kimia',
        'Fisika',
        'Ekonomi',
        'Sosiologi',
        'Geografi',
    ];

    public function homepage()
    {
        return view('index', ['active_page' => 'beranda']);
    }

    // === DINONAKTIFKAN SEMENTARA — menunggu data asli & validasi algoritma ===
    // public function prediksiPtn(Request $request)
    // {
    //     $ptns = Ptn::with('prodi')->orderBy('ptn')->get();
    //     $selectedPtn = null;
    //     $selectedProdi = null;
    //     $result = null;
    //
    //     if ($ptns->isNotEmpty()) {
    //         $ptnId = (int) ($request->input('ptn_id') ?? $request->query('ptn_id') ?? $ptns->first()->id);
    //         $selectedPtn = $ptns->firstWhere('id', $ptnId) ?? $ptns->first();
    //         $selectedProdi = $selectedPtn?->prodi->firstWhere('id', (int) ($request->input('prodi_id') ?? $request->query('prodi_id')))
    //             ?? $selectedPtn?->prodi->first();
    //     }
    //
    //     if ($request->isMethod('post')) {
    //         $validated = $request->validate([
    //             'ptn_id' => 'required|exists:ptn,id',
    //             'prodi_id' => 'required|exists:ptn_prodi,id',
    //             'nilai' => 'required|array',
    //         ]);
    //
    //         $selectedPtn = Ptn::with('prodi')->findOrFail($validated['ptn_id']);
    //         $selectedProdi = $selectedPtn->prodi()->findOrFail($validated['prodi_id']);
    //
    //         $score = $this->calculateFinalScore($validated['nilai'], $selectedProdi->prodi);
    //         $gradeLimit = max(0, (float) $selectedProdi->grade);
    //         $persaingan = (float) $selectedProdi->persen * 100;
    //         $status = $score >= $gradeLimit ? 'kompetitif' : 'alternatif';
    //         $peluang = $this->calculateOpportunity($score, $gradeLimit);
    //         $result = [
    //             'skor' => round($score, 2),
    //             'batas_minimum' => round($gradeLimit, 2),
    //             'selisih' => round($score - $gradeLimit, 2),
    //             'tingkat_persaingan' => round($persaingan, 2),
    //             'peluang_lolos' => round($peluang, 2),
    //             'status' => $status,
    //             'status_label' => $score >= $gradeLimit ? '🟢 KOMPETITIF' : '🟡 ALTERNATIF',
    //             'alternatif' => $this->getAlternativeProdi($selectedPtn, $validated['nilai'], $selectedProdi->id),
    //         ];
    //     }
    //
    //     return view('prediksi_ptn', [
    //         'active_page' => 'prediksi',
    //         'ptns' => $ptns,
    //         'selectedPtn' => $selectedPtn,
    //         'selectedProdi' => $selectedProdi,
    //         'result' => $result,
    //         'mapelList' => $this->mapelRapor,
    //         'semesterList' => [1, 2, 3, 4, 5],
    //     ]);
    // }
    // === AKTIFKAN KEMBALI SETELAH DATA SIAP ===

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

    public function classroomDetail(int $id)
    {
        $sekolah = \App\Models\Sekolah::with([
            'jenjang',
            'kotaKabupaten',
        ])
            ->where('is_active', 1)
            ->findOrFail($id);

        return view('sekolah_detail', [
            'active_page' => 'classroom',
            'sekolah' => $sekolah,
        ]);
    }

    // ==================================================
    // ARTIKEL BUDAYA LAMPUNG
    // ==================================================

    public function artikel()
    {
        return view('artikel', ['active_page' => 'artikel']);
    }

    public function artikelKategori(string $slug)
    {
        $kategori = ArtikelKategori::where('slug', $slug)->firstOrFail();

        return view('artikel', [
            'active_page' => 'artikel',
            'kategori_slug' => $kategori->slug,
            'kategori_nama' => $kategori->nama,
        ]);
    }

    public function artikelDetail(string $slug)
    {
        $artikel = Artikel::with('kategori')
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        return view('artikel_detail', [
            'active_page' => 'artikel',
            'artikel' => $artikel,
        ]);
    }

    // === DINONAKTIFKAN SEMENTARA — menunggu data asli & validasi algoritma ===
    // private function calculateFinalScore(array $nilai, ?string $prodiName = null): float
    // {
    //     $scores = [];
    //
    //     // Hitung rata-rata tiap mata pelajaran dari 5 semester, pastikan setiap nilai 0-100
    //     foreach ($this->mapelRapor as $mapel) {
    //         $semesterScores = [];
    //         foreach ([1, 2, 3, 4, 5] as $semester) {
    //             $value = (float) ($nilai[$mapel][$semester] ?? 0);
    //             $semesterScores[] = max(0, min(100, $value));
    //         }
    //
    //         $scores[$mapel] = count($semesterScores) ? array_sum($semesterScores) / count($semesterScores) : 0;
    //     }
    //
    //     // Komponen pertama: rata-rata seluruh mata pelajaran (semester 1-5)
    //     $overallAverage = count($scores) ? array_sum($scores) / count($scores) : 0;
    //
    //     // Komponen kedua: maksimal 2 mata pelajaran pendukung yang relevan
    //     $relevantMapels = $this->getRelevantMapels($prodiName ?? '');
    //     $relevantScores = [];
    //
    //     foreach ($relevantMapels as $mapel) {
    //         // jika mapel relevan tidak ada di daftar rapor, gunakan overallAverage sebagai fallback
    //         $relevantScores[] = $scores[$mapel] ?? $overallAverage;
    //     }
    //
    //     // ambil dua nilai pendukung terbaik (jika ada) — ini sesuai aturan "maksimal 2 mata pelajaran pendukung"
    //     rsort($relevantScores);
    //     $topSupport = array_slice($relevantScores, 0, 2);
    //     $supportiveAverage = count($topSupport) ? array_sum($topSupport) / count($topSupport) : $overallAverage;
    //
    //     // Bobot: overall minimal 50%, supportive maksimal 50%
    //     return (($overallAverage * 0.5) + ($supportiveAverage * 0.5));
    // }
    //
    // private function getRelevantMapels(string $prodiName): array
    // {
    //     $label = strtolower($prodiName);
    //
    //     if (preg_match('/dokter|kedokteran|farmasi|kesehatan|gizi|biologi/', $label)) {
    //         return ['Matematika', 'Biologi', 'Kimia', 'Bahasa Inggris'];
    //     }
    //
    //     if (preg_match('/informatika|teknik|sipil|mesin|elektro|arsitektur|fisika|kimia/', $label)) {
    //         return ['Matematika', 'Fisika', 'Bahasa Inggris'];
    //     }
    //
    //     if (preg_match('/ekonomi|akuntansi|manajemen|bisnis|administrasi/', $label)) {
    //         return ['Matematika', 'Ekonomi', 'Bahasa Inggris'];
    //     }
    //
    //     if (preg_match('/psikologi|sosial|hukum|politik|publik/', $label)) {
    //         return ['Bahasa Indonesia', 'Sosiologi', 'Bahasa Inggris'];
    //     }
    //
    //     return ['Matematika', 'Bahasa Inggris'];
    // }
    //
    // private function normalizeGrade(float $grade): float
    // {
    //     $grade = max(0, (float) $grade);
    //
    //     // Already a percentage (0-100)
    //     if ($grade <= 100) {
    //         return $grade;
    //     }
    //
    //     // If grade looks like a sum of 5 semester scores (0-500), convert to 0-100 scale
    //     if ($grade <= 500) {
    //         return round($grade / 5, 2);
    //     }
    //
    //     // Fallback: adaptively scale down large unexpected values to 0-100
    //     $divisor = (int) ceil($grade / 100);
    //     return min(round($grade / $divisor, 2), 100);
    // }
    //
    // private function calculateOpportunity(float $score, float $minimum): float
    // {
    //     if ($minimum <= 0) {
    //         return 0;
    //     }
    //
    //     return max(0, min(100, ($score / $minimum) * 100));
    // }
    //
    // private function getAlternativeProdi(Ptn $ptn, array $nilai, int $excludeId = 0): array
    // {
    //     $prodiList = $ptn->prodi()->where('id', '!=', $excludeId)->orderByDesc('grade')->get();
    //     $alternatif = [];
    //
    //     foreach ($prodiList as $prodi) {
    //         $score = $this->calculateFinalScore($nilai, $prodi->prodi);
    //         // Use raw grade from database for alternative filtering
    //         $gradeLimit = max(0, (float) $prodi->grade);
    //
    //         if ($score >= $gradeLimit) {
    //             $alternatif[] = [
    //                 'id' => $prodi->id,
    //                 'prodi' => $prodi->prodi,
    //                 'grade' => round($gradeLimit, 2),
    //                 'skor' => round($score, 2),
    //                 'selisih' => round($score - $gradeLimit, 2),
    //                 'persen' => round((float) $prodi->persen * 100, 2),
    //             ];
    //         }
    //     }
    //
    //     usort($alternatif, fn (array $a, array $b) => $b['skor'] <=> $a['skor']);
    //
    //     return array_slice($alternatif, 0, 3);
    // }
    // === AKTIFKAN KEMBALI SETELAH DATA SIAP ===
}