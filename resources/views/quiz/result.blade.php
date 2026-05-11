{{-- resources/views/quiz/result.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ujian</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.css">
    <script>
        (function () {
            const saved     = localStorage.getItem('quiz-theme');
            const preferred = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
            document.documentElement.dataset.theme = saved ?? preferred;
        })();
    </script>
    <style>
        body { display: block; padding: 0; }

        /* Override CSS global agar lebar seragam */
        .result-breakdown {
            max-width: 100% !important;
            width: 100% !important;
            box-sizing: border-box;
        }

        /* Passage — tampilan identik dengan story-block di halaman quiz */
        .result-story-block {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            margin-bottom: 16px;
        }
        .result-story-text {
            padding: 20px 24px;
        }
        .result-story-text p {
            font-family: var(--ff-display);
            font-size: 15px;
            line-height: 1.85;
            color: var(--text);
            font-weight: 400;
        }
        .result-story-text p + p { margin-top: 12px; }
        .result-story-text blockquote {
            border-left: 3px solid var(--gold);
            margin: 16px 0;
            padding: 4px 16px;
            font-style: italic;
            color: var(--gold-light);
            font-size: 15px;
        }
        .result-story-text table {
            border-collapse: collapse;
            width: 100%;
            font-size: 13px;
            margin: 12px 0;
            overflow-x: auto;
            display: block;
        }
        .result-story-text table th,
        .result-story-text table td {
            border: 1px solid rgba(255,255,255,0.15);
            padding: 7px 10px;
            text-align: left;
            white-space: nowrap;
        }
        .result-story-text table th {
            background: rgba(255,255,255,0.07);
            font-weight: 500;
        }
        .result-story-text figcaption {
            font-size: 12px;
            margin-bottom: 6px;
            font-style: italic;
            color: var(--text-muted);
        }

        /* Review block */
        .review-block {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            margin-bottom: 16px;
            overflow: hidden;
        }
        .review-block-header {
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .review-block-num {
            font-size: 12px;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .badge-benar {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 100px;
            background: rgba(74,158,106,0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }
        .badge-salah {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 100px;
            background: rgba(220,80,80,0.1);
            color: #e05555;
            border: 1px solid #e05555;
        }
        .badge-kosong {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 100px;
            background: rgba(150,150,150,0.1);
            color: var(--text-dim);
            border: 1px solid var(--border);
        }
        .review-block-body { padding: 16px 20px; }
        .review-question-text {
            font-size: 15px;
            line-height: 1.7;
            color: var(--text);
            margin-bottom: 14px;
        }
        .review-option {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 8px;
            border: 1px solid var(--border);
            font-size: 14px;
            line-height: 1.5;
        }
        .review-option.pilihan-siswa {
            border-color: #e05555;
            background: rgba(220,80,80,0.06);
        }
        .review-option.jawaban-benar {
            border-color: var(--success);
            background: rgba(74,158,106,0.08);
        }
        .review-option.pilihan-siswa.jawaban-benar {
            border-color: var(--success);
            background: rgba(74,158,106,0.08);
        }
        .opt-key {
            min-width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            background: var(--surface2);
            color: var(--text-dim);
            flex-shrink: 0;
        }
        .review-option.jawaban-benar .opt-key { background: var(--success); color: #fff; }
        .review-option.pilihan-siswa:not(.jawaban-benar) .opt-key { background: #e05555; color: #fff; }
        .opt-label { color: var(--text); flex: 1; min-width: 0; word-break: break-word; }
        .opt-tag {
            margin-left: auto;
            font-size: 11px;
            white-space: nowrap;
            flex-shrink: 0;
            color: var(--text-dim);
        }
        .review-option.jawaban-benar .opt-tag { color: var(--success); }
        .review-option.pilihan-siswa:not(.jawaban-benar) .opt-tag { color: #e05555; }
        .tidak-dijawab-note {
            font-size: 13px;
            color: var(--text-dim);
            font-style: italic;
            margin-top: 8px;
        }

        /* Mobile */
        @media (max-width: 640px) {
            .review-block-body   { padding: 12px 14px; }
            .review-block-header { padding: 10px 14px; }
            .review-option       { padding: 8px 10px; font-size: 13px; }
            .opt-tag             { display: none; }
            .result-story-text   { padding: 14px 16px; }
            .result-story-text p { font-size: 13px; }
        }
    </style>
</head>
<body>

{{-- Theme toggle --}}
<button class="theme-toggle" onclick="toggleTheme()" title="Ganti tema"
        style="position:fixed; top:16px; right:16px; z-index:999;">
    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
        <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
    </svg>
    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
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


<section style="min-height:100vh; padding:60px 20px 80px;">

    <div style="max-width:640px; margin:0 auto; width:100%; box-sizing:border-box;">

        {{-- Score ring --}}
        <div style="text-align:center; margin-bottom:32px;">
            <div class="score-ring">
                <span class="score-num">{{ $hasil->correct_count }}</span>
                <span class="score-denom">dari {{ $hasil->total_questions }}</span>
            </div>

            @php
                $pct    = $hasil->total_questions > 0
                            ? round(($hasil->correct_count / $hasil->total_questions) * 100) : 0;
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
        </div>

        {{-- Breakdown Per Mata Pelajaran --}}
        <div class="result-breakdown" style="margin-bottom:32px;">
            <p class="breakdown-title">Nilai Per Mata Pelajaran</p>

            @foreach($subjectBreakdown as $mapel)
            @php
                $pctMapel = $mapel['total'] > 0
                    ? round(($mapel['correct'] / $mapel['total']) * 100)
                    : 0;
                $barColor = $pctMapel >= 75
                    ? 'var(--success)'
                    : ($pctMapel >= 50 ? 'var(--gold)' : '#e05555');
            @endphp
            <div style="padding:14px 0;border-bottom:1px solid var(--border);">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <span style="font-size:14px;font-weight:500;color:var(--text);">
                        {{ $mapel['label'] }}
                    </span>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span style="font-size:12px;color:var(--text-muted);">
                            {{ $mapel['correct'] }} / {{ $mapel['total'] }} benar
                        </span>
                        <span style="
                            font-size:15px;
                            font-weight:600;
                            color:{{ $barColor }};
                            min-width:44px;
                            text-align:right;
                        ">
                            {{ $pctMapel }}%
                        </span>
                    </div>
                </div>
                {{-- Progress bar --}}
                <div style="
                    height:6px;
                    border-radius:100px;
                    background:var(--surface2, rgba(255,255,255,0.07));
                    overflow:hidden;
                ">
                    <div style="
                        height:100%;
                        border-radius:100px;
                        width:{{ $pctMapel }}%;
                        background:{{ $barColor }};
                        transition:width .4s ease;
                    "></div>
                </div>
                <div style="margin-top:6px;font-size:12px;color:var(--text-dim);">
                    Poin diperoleh: <strong style="color:var(--gold);">{{ $mapel['points'] }}</strong>
                </div>
            </div>
            @endforeach

            {{-- Total keseluruhan --}}
            <div style="
                display:flex;justify-content:space-between;align-items:center;
                padding-top:14px;
            ">
                <span style="font-size:13px;color:var(--text-muted);">Total Keseluruhan</span>
                <div style="display:flex;align-items:center;gap:16px;">
                    <span style="font-size:13px;color:var(--text-muted);">
                        {{ $hasil->correct_count }} / {{ $hasil->total_questions }} benar
                    </span>
                    <span style="font-size:18px;font-weight:700;color:var(--gold);">
                        {{ $hasil->score }} poin
                    </span>
                </div>
            </div>
        </div>

        {{-- Ringkasan Ujian --}}
        <div class="result-breakdown" style="margin-bottom:32px;">
            <p class="breakdown-title">Ringkasan Ujian</p>
            <div class="breakdown-item">
                <span class="q-text">Paket Soal</span>
                <span class="q-status" style="color:var(--gold);">{{ $hasil->session->paket ?? '-' }}</span>
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
                <span class="q-status" style="color:var(--text-muted);font-size:12px;">
                    {{ $hasil->submitted_at?->format('d M Y, H:i') ?? '-' }}
                </span>
            </div>
        </div>

        {{-- Review Jawaban --}}
        <p class="breakdown-title" style="margin-bottom:16px;">Review Jawaban</p>

        @php $qNum = 0; @endphp

        @foreach($questions as $question)
            @php
                $qNum++;

                // ── Logika passage identik dengan _questions.blade.php ──
                $passageContent = $question->passage_highlighted
                    ?? ($question->passage ? $question->passage->content : null);

                $prevQuestion = $loop->first ? null : $questions[$loop->index - 1];
                $isNewPassage = $loop->first
                    || $question->passage_id !== $prevQuestion->passage_id
                    || $question->passage_highlighted !== $prevQuestion->passage_highlighted;

                $passageText = trim(strip_tags($passageContent ?? ''));
                $hasPassage  = !empty($passageText) && strtolower($passageText) !== 'nan';

                // ── Data jawaban siswa ──
                $siswaAnswer  = $answers->get($question->id);
                $pilihanSiswa = $siswaAnswer?->answer;
                $jawabanBenar = $question->correct_answer;
                $isCorrect    = $siswaAnswer?->is_correct ?? false;
                $tidakDijawab = is_null($pilihanSiswa);
            @endphp

            {{-- Teks Bacaan — tampil sesuai kondisi yang sama dengan halaman quiz --}}
            @if($isNewPassage && $hasPassage)
                <div class="result-story-block">
                    <div class="result-story-text">
                        {!! $passageContent !!}
                    </div>
                </div>
            @endif

            {{-- Soal --}}
            <div class="review-block">
                <div class="review-block-header">
                    <span class="review-block-num">Soal {{ $qNum }}</span>
                    @if($tidakDijawab)
                        <span class="badge-kosong">Tidak Dijawab</span>
                    @elseif($isCorrect)
                        <span class="badge-benar">✓ Benar</span>
                    @else
                        <span class="badge-salah">✗ Salah</span>
                    @endif
                </div>

                <div class="review-block-body">

                    {{-- Pertanyaan --}}
                    <div class="review-question-text">{!! $question->question_text !!}</div>

                    {{-- Pilihan Jawaban --}}
                    @foreach(['A','B','C','D','E'] as $opt)
                        @php $col = 'option_' . strtolower($opt); @endphp
                        @if($question->$col)
                            @php
                                $isBenar   = $opt === $jawabanBenar;
                                $isPilihan = $opt === $pilihanSiswa;
                                $classes   = 'review-option';
                                if ($isPilihan && $isBenar)  $classes .= ' pilihan-siswa jawaban-benar';
                                elseif ($isBenar)             $classes .= ' jawaban-benar';
                                elseif ($isPilihan)           $classes .= ' pilihan-siswa';
                            @endphp
                            <div class="{{ $classes }}">
                                <span class="opt-key">{{ $opt }}</span>
                                <span class="opt-label">{!! $question->$col !!}</span>
                                <span class="opt-tag">
                                    @if($isPilihan && $isBenar)
                                        ✓ Jawaban Anda &amp; Kunci
                                    @elseif($isBenar)
                                        ✓ Kunci Jawaban
                                    @elseif($isPilihan)
                                        ✗ Jawaban Anda
                                    @endif
                                </span>
                            </div>
                        @endif
                    @endforeach

                    @if($tidakDijawab)
                        <p class="tidak-dijawab-note">
                            Soal ini tidak dijawab. Kunci jawaban: <strong>{{ $jawabanBenar }}</strong>
                        </p>
                    @endif

                </div>
            </div>

        @endforeach

        {{-- Tombol Kembali --}}
        <div class="result-actions" style="margin-top:24px;">
            <a href="{{ route('quiz.index') }}" class="btn-primary">
                Kembali ke Dashboard
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor"
                          stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/contrib/auto-render.min.js"></script>
<script>
function toggleTheme() {
    const html = document.documentElement;
    html.dataset.theme = html.dataset.theme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('quiz-theme', html.dataset.theme);
}
document.addEventListener("DOMContentLoaded", () => {
    renderMathInElement(document.body, {
        delimiters: [
            { left: "$$", right: "$$", display: true  },
            { left: "$",  right: "$",  display: false },
        ]
    });
});
</script>
</body>
</html>
