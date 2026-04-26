{{-- resources/views/quiz.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quiz — Paket {{ $paket }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

{{-- ══ SCREEN: START ══ --}}
<section id="screen-start" class="screen active">
    <span class="hero-badge">Quiz Interaktif</span>
    <h1 class="hero-title anim">Quiz <em>Paket {{ $paket }}</em></h1>
    <p class="hero-sub anim anim-d1">Baca setiap teks bacaan, lalu jawab pertanyaan yang tersedia.</p>

    <div class="hero-meta anim anim-d3">
        <div class="meta-item">
            <span class="meta-val">{{ $groups->count() }}</span>
            <span class="meta-label">Teks Bacaan</span>
        </div>
        <div class="meta-item">
            <span class="meta-val">{{ $totalQuestions }}</span>
            <span class="meta-label">Pertanyaan</span>
        </div>
        <div class="meta-item">
            <span class="meta-val">{{ $totalPoints }}</span>
            <span class="meta-label">Total Poin</span>
        </div>
    </div>

    <div class="anim anim-d4" style="display:flex; gap:12px; flex-wrap:wrap; justify-content:center;">
        <button class="btn-primary" onclick="startQuiz()">
            Mulai Quiz
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</section>

{{-- ══ PROGRESS BAR ══ --}}
<div class="progress-wrap" id="progress-wrap" style="display:none;">
    <div class="progress-track">
        <div class="progress-fill" id="progress-fill" style="width:0%"></div>
    </div>
    <span class="progress-text" id="progress-text">0 / {{ $totalQuestions }}</span>
</div>

{{-- ══ SCREEN: SOAL ══ --}}
<section id="screen-quiz" class="screen" style="padding-top:0; display:none;">

    @php $qNum = 0; @endphp

    @foreach($questions as $question)
        @php $qNum++; @endphp

        {{-- Tampilkan teks bacaan jika soal pertama atau passage berbeda dari soal sebelumnya --}}
        @if($loop->first || $question->passage_id !== $questions[$loop->index - 1]->passage_id)
        <div class="story-block anim">
            <div class="story-text">
                {{-- Pakai highlighted jika ada, fallback ke content --}}
                {!! $question->passage_highlighted
                    ?? ($question->passage ? $question->passage->content : '') !!}
            </div>
        </div>
        @elseif($question->passage_highlighted)
        {{-- Passage sama tapi soal ini punya versi highlighted --}}
        <div class="story-block anim">
            <div class="story-text">
                {!! $question->passage_highlighted !!}
            </div>
        </div>
        @endif

        {{-- SOAL --}}
        <div class="question-block anim" id="question-{{ $question->id }}">
            <p class="question-num">Soal {{ $qNum }} dari {{ $totalQuestions }}</p>
            <h3 class="question-text">{!! $question->question_text !!}</h3>

            <div class="options-list" id="opts-{{ $question->id }}">
                @foreach(['A','B','C','D','E'] as $opt)
                    @php $col = 'option_' . strtolower($opt); @endphp
                    @if($question->$col)
                    <button class="option-btn"
                            onclick="selectOption(this, {{ $question->id }}, '{{ $opt }}', {{ $totalQuestions }})"
                            data-q="{{ $question->id }}">
                        <span class="option-key">{{ $opt }}</span>
                        <span class="option-label">{{ $question->$col }}</span>
                    </button>
                    @endif
                @endforeach
            </div>

            <div class="feedback-box" id="fb-{{ $question->id }}">
                <p class="feedback-title">—</p>
                <p class="feedback-text"></p>
            </div>
        </div>

    @endforeach

    <div class="nav-row">
        <button class="btn-primary" onclick="showResult()" id="btn-finish" style="opacity:0.4; pointer-events:none;">
            Lihat Hasil
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 3l5 5-5 5M3 8h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</section>

{{-- ══ SCREEN: RESULT ══ --}}
<section id="screen-result" class="screen" style="display:none;">
    <div class="divider"></div>
    <div class="score-ring">
        <span class="score-num" id="score-num">0</span>
        <span class="score-denom" id="score-denom">dari {{ $totalQuestions }}</span>
    </div>
    <h2 class="result-title" id="result-title">—</h2>
    <p class="result-desc" id="result-desc"></p>

    <div class="result-breakdown">
        <p class="breakdown-title">Ringkasan Jawaban</p>
        <div id="breakdown-list"></div>
    </div>

    <div class="result-actions">
        <button class="btn-primary" onclick="location.reload()">
            Main Lagi
        </button>
    </div>
</section>

<script>
    const TOTAL = {{ $totalQuestions }};
    const CHECK_URL = '{{ route("quiz.check") }}';
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let answered = 0;
    let results = {};
</script>
<script src="{{ asset('js/quiz.js') }}"></script>
</body>
</html>