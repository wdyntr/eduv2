{{-- resources/views/quiz/partials/_progress.blade.php --}}
<div class="progress-wrap" id="progress-wrap" style="display:none;">

    {{-- Hamburger — hanya muncul di mobile via CSS --}}
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

</div>