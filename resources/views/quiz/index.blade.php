{{-- resources/views/quiz/index.blade.php --}}
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

{{-- Overlay (mobile drawer backdrop) --}}
<div id="menu-overlay" onclick="closeMenu()"></div>

{{-- Slide-in Drawer (mobile: hamburger → drawer; desktop: hidden) --}}
<aside id="menu-drawer" aria-hidden="true">
    <div class="drawer-header">
        <span class="drawer-header-title">Navigasi</span>
        <button class="drawer-close" onclick="closeMenu()" aria-label="Tutup menu">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="1" y1="1" x2="13" y2="13"/>
                <line x1="13" y1="1" x2="1" y2="13"/>
            </svg>
        </button>
    </div>

    <div class="drawer-theme-row">
        <span class="drawer-section-label">Tema tampilan</span>
        <button class="theme-toggle theme-toggle-drawer" onclick="toggleTheme()"
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
    </div>

    {{-- Question nav akan dipindahkan JS ke sini saat quiz dimulai di mobile --}}
    <div id="drawer-nav-slot"></div>
</aside>

{{-- Theme toggle FIXED — hanya tampil di desktop --}}
<button class="theme-toggle theme-toggle-fixed" id="theme-toggle-fixed"
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

@include('quiz.partials._start')
@include('quiz.partials._progress')

<div id="quiz-wrapper" style="display:none;">
    @include('quiz.partials._question-nav')
    @include('quiz.partials._questions')
</div>

@include('quiz.partials._result')

<script>
    const TOTAL      = {{ $totalQuestions }};
    const SUBMIT_URL = '{{ route("quiz.submit") }}';
    const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
</script>
<script src="{{ asset('js/quiz.js') }}"></script>
</body>
</html>