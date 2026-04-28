{{-- resources/views/quiz/partials/_start.blade.php --}}
<section id="screen-start" class="screen active">

    {{-- Toggle di start screen, pojok kanan atas --}}
    <button class="theme-toggle theme-toggle-start" onclick="toggleTheme()"
            title="Ganti tema" aria-label="Toggle tema">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
        </svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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