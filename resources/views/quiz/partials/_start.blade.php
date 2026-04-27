{{-- resources/views/quiz/partials/_start.blade.php --}}
<section id="screen-start" class="screen active">
    <span class="hero-badge">Quiz Interaktif</span>
    <h1 class="hero-title anim">Quiz <em>Paket {{ $paket }}</em></h1>
    <p class="hero-sub anim anim-d1">Baca setiap teks bacaan, lalu jawab semua pertanyaan.</p>

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

    <div class="anim anim-d4" style="display:flex; gap:12px; justify-content:center;">
        <button class="btn-primary" onclick="startQuiz()">
            Mulai Quiz
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</section>