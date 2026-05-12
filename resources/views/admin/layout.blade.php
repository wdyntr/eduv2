{{-- resources/views/admin/layout.blade.php --}}
<!DOCTYPE html>
<html lang="id">  {{-- ← hapus data-theme="light" --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.css">


    {{-- Terapkan tema SEBELUM render untuk cegah flash --}}
    <script>
        (function () {
            const saved     = localStorage.getItem('quiz-theme');
            const preferred = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
            document.documentElement.dataset.theme = saved ?? preferred;
        })();
    </script>
</head>
<body class="admin-body">

<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <span class="brand-text">Quiz<em>Admin</em></span>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Kelola User
        </a>
        <a href="{{ route('admin.sessions.index') }}"
           class="nav-link {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Sesi Ujian
        </a>
        <a href="{{ route('admin.results.index') }}"
           class="nav-link {{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Hasil Ujian
        </a>
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="sidebar-footer">
        @csrf
        <button type="submit" class="nav-link nav-logout">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Keluar
        </button>
    </form>
</aside>

<main class="admin-main">
    <div class="admin-topbar">
        {{-- ← Tambahkan hamburger button --}}
        <button class="admin-hamburger" id="admin-hamburger" onclick="toggleSidebar()" aria-label="Buka menu">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <line x1="2" y1="5"  x2="18" y2="5"/>
                <line x1="2" y1="10" x2="18" y2="10"/>
                <line x1="2" y1="15" x2="18" y2="15"/>
            </svg>
        </button>

        <h1 class="admin-page-title">@yield('title')</h1>
        {{-- ↓ Theme toggle di sini --}}
        <div style="display:flex; align-items:center; gap:12px;">
            <span class="admin-user">{{ auth()->user()->name }}</span>
            <button class="theme-toggle admin-theme-btn" onclick="toggleTheme()" title="Ganti tema">
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
                </svg>
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1"  x2="12" y2="3"/>
                    <line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22"   x2="5.64" y2="5.64"/>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1"  y1="12" x2="3"  y2="12"/>
                    <line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                    <line x1="18.36" y1="5.64"  x2="19.78" y2="4.22"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ← Tambahkan overlay untuk sidebar mobile --}}
    <div class="admin-sidebar-overlay" id="admin-sidebar-overlay" onclick="toggleSidebar()"></div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="admin-content">
        @yield('content')
    </div>
</main>

<script>
function toggleTheme() {
    const html = document.documentElement;
    const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
    html.dataset.theme = next;
    localStorage.setItem('quiz-theme', next);
}
</script>
<script src="{{ asset('js/admin.js') }}"></script>
{{-- KaTeX JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/contrib/auto-render.min.js"></script>
@yield('scripts')
</body>
</html>
