{{-- resources/views/quiz/result.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ujian</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        (function () {
            const saved     = localStorage.getItem('quiz-theme');
            const preferred = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
            document.documentElement.dataset.theme = saved ?? preferred;
        })();
    </script>
    <style>
        /* Override: body di result tidak perlu flex center */
        body {
            display: block;
            padding: 0;
        }
    </style>
</head>
<body>

{{-- Toggle: fixed top-right, tidak ikut flow flex --}}
<button class="theme-toggle" onclick="toggleTheme()" title="Ganti tema"
        style="position:fixed; top:16px; right:16px; z-index:999;">
    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="1.8" stroke-linecap="round">
        <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
    </svg>
    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="1.8" stroke-linecap="round">
        <circle cx="12" cy="12" r="5"/>
        <line x1="12" y1="1"  x2="12" y2="3"/>
        <line x1="12" y1="21" x2="12" y2="23"/>
        <line x1="4.22" y1="4.22"   x2="5.64" y2="5.64"/>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
        <line x1="1"  y1="12" x2="3"  y2="12"/>
        <line x1="21" y1="12" x2="23" y2="12"/>
        <line x1="4.22" y1="19.78"  x2="5.64" y2="18.36"/>
        <line x1="18.36" y1="5.64"  x2="19.78" y2="4.22"/>
    </svg>
</button>

<section style="
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 60px 20px 80px;
    text-align: center;
">
    <div class="divider"></div>

    {{-- Score ring --}}
    <div class="score-ring">
        <span class="score-num">{{ $hasil->correct_count }}</span>
        <span class="score-denom">dari {{ $hasil->total_questions }}</span>
    </div>

    @php
        $pct    = $hasil->total_questions > 0
                    ? round(($hasil->correct_count / $hasil->total_questions) * 100)
                    : 0;
        $idx    = min((int) round($pct / 25), 4);
        $titles = ['Coba Lagi!', 'Terus Berlatih!', 'Hampir!', 'Bagus!', 'Sempurna!'];
        $descs  = [
            'Jangan menyerah, pelajari lagi materinya.',
            'Kamu sudah cukup paham, tingkatkan lagi!',
            'Sedikit lagi sempurna!',
            'Kerja bagus, terus pertahankan!',
            'Luar biasa! Semua jawaban benar.',
        ];
    @endphp

    <h2 class="result-title">{{ $titles[$idx] }}</h2>
    <p class="result-desc">{{ $descs[$idx] }}</p>

    {{-- ── Rekap skor ── --}}
    <div class="result-breakdown" style="max-width:460px; width:100%; text-align:left; margin-bottom:20px;">
        <p class="breakdown-title">Ringkasan Ujian</p>

        <div class="breakdown-item">
            <span class="q-text">Mata Pelajaran</span>
            <span class="q-status" style="color:var(--gold);">
                {{ ucfirst(str_replace('_', ' ', $hasil->session->subject ?? '-')) }}
            </span>
        </div>
        <div class="breakdown-item">
            <span class="q-text">Paket Soal</span>
            <span class="q-status" style="color:var(--gold);">
                {{ $hasil->session->paket ?? '-' }}
            </span>
        </div>
        <div class="breakdown-item">
            <span class="q-text">Jawaban Benar</span>
            <span class="q-status ok">{{ $hasil->correct_count }}</span>
        </div>
        <div class="breakdown-item">
            <span class="q-text">Jawaban Salah</span>
            <span class="q-status no">{{ $hasil->total_questions - $hasil->correct_count }}</span>
        </div>
        <div class="breakdown-item">
            <span class="q-text">Total Poin</span>
            <span class="q-status" style="color:var(--gold);">{{ $hasil->score }}</span>
        </div>
        <div class="breakdown-item">
            <span class="q-text">Persentase</span>
            <span class="q-status" style="color:var(--gold);">{{ $pct }}%</span>
        </div>
        <div class="breakdown-item">
            <span class="q-text">Dikumpulkan</span>
            <span class="q-status" style="color:var(--text-muted); font-size:12px;">
                {{ $hasil->submitted_at?->format('d M Y, H:i') ?? '-' }}
            </span>
        </div>
    </div>

    {{-- ── Ringkasan per soal ── --}}
    @php
        $answers = $hasil->session
            ->answers()
            ->where('user_id', auth()->id())
            ->with('question')
            ->orderBy('question_id')
            ->get();
    @endphp

    <!-- @if($answers->isNotEmpty())
    <div class="result-breakdown" style="max-width:460px; width:100%; text-align:left; margin-bottom:20px;">
        <p class="breakdown-title">Detail per Soal</p>

        @foreach($answers as $i => $ans)
        <div class="breakdown-item">
            <span class="q-text">
                Soal {{ $i + 1 }}
                &nbsp;·&nbsp;
                Jawaban: <strong>{{ $ans->answer }}</strong>
                &nbsp;·&nbsp;
                Benar: <strong>{{ $ans->question->correct_answer ?? '?' }}</strong>
            </span>
            <span class="q-status {{ $ans->is_correct ? 'ok' : 'no' }}">
                {{ $ans->is_correct ? 'Benar' : 'Salah' }}
            </span>
        </div>
        @endforeach
    </div>
    @endif -->

    {{-- ── Aksi ── --}}
    <div class="result-actions">
        <a href="{{ route('quiz.index') }}" class="btn-primary">
            Kembali ke Dashboard
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor"
                      stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>
</section>

<script>
function toggleTheme() {
    const html = document.documentElement;
    html.dataset.theme = html.dataset.theme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('quiz-theme', html.dataset.theme);
}
</script>
</body>
</html>