{{-- resources/views/quiz/partials/_progress.blade.php --}}
<div class="progress-wrap" id="progress-wrap" style="display:none;">

    {{-- Hamburger — hanya muncul di mobile --}}
    <button class="hamburger-btn" id="hamburger-btn"
            onclick="openMenu()" aria-label="Buka navigasi soal">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
             stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
            <line x1="2" y1="4.5"  x2="16" y2="4.5"/>
            <line x1="2" y1="9"    x2="16" y2="9"/>
            <line x1="2" y1="13.5" x2="16" y2="13.5"/>
        </svg>
        <span id="hamburger-badge" class="hamburger-badge"></span>
    </button>

    <div class="progress-track">
        <div class="progress-fill" id="progress-fill" style="width:0%"></div>
    </div>

    <span class="progress-text" id="progress-text">0 / {{ $totalQuestions }}</span>

    {{-- Toggle di kanan progress bar — hanya desktop --}}
    <button class="theme-toggle theme-toggle-bar" id="theme-toggle-bar"
            onclick="toggleTheme()" title="Ganti tema" aria-label="Toggle tema">
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

</div>