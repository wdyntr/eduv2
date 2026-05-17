<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quiz Interaktif')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script>
        (function () {
            const saved     = localStorage.getItem('quiz-theme');
            const preferred = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
            document.documentElement.dataset.theme = saved ?? preferred;
        })();
    </script>
</head>
<body>

{{-- Topbar --}}
<div style="
    position: sticky; top: 0; z-index: 100;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
    padding: 12px 24px;
    display: flex; align-items: center; justify-content: space-between;
    width: 100%; box-sizing: border-box;
">
    <span style="font-family:var(--ff-display);font-size:18px;color:var(--text);">
        Quiz <em style="color:var(--gold);">Interaktif</em>
    </span>

    <div style="display:flex;align-items:center;gap:14px;">
        <span style="font-size:13px;color:var(--text-muted);">
            {{ auth()->user()->name }}
            @if(auth()->user()->kelas)
                <span style="color:var(--text-dim);">· {{ auth()->user()->kelas }}</span>
            @endif
        </span>

        {{-- Theme toggle --}}
        <button class="theme-toggle" onclick="toggleTheme()" title="Ganti tema"
                style="width:34px;height:34px;">
            <svg class="icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
            </svg>
            <svg class="icon-moon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
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

        {{-- Logout — pakai safeLogout ──────────────── --}}
        <form method="POST" action="{{ route('logout') }}"
              style="display:inline;" id="logout-form-siswa">
            @csrf
            <button type="button" onclick="safeLogout('logout-form-siswa')"
                    class="btn-ghost" style="padding:7px 14px;font-size:13px;">
                Keluar
            </button>
        </form>
    </div>
</div>

{{-- Content --}}
<div style="width:100%;max-width:720px;margin:0 auto;padding:32px 20px 80px;box-sizing:border-box;">
    @yield('content')
</div>

<script>
function toggleTheme() {
    const html = document.documentElement;
    const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
    html.dataset.theme = next;
    localStorage.setItem('quiz-theme', next);
}

// ── SESSION KEEPALIVE ──
setInterval(() => {
    fetch(window.location.href, {
        method: 'HEAD',
        credentials: 'same-origin',
    }).catch(() => {});
}, 10 * 60 * 1000);

// ── SAFE LOGOUT ──
async function safeLogout(formId) {
    try {
        const res  = await fetch('/logout-token');
        const data = await res.json();
        const form = document.getElementById(formId);
        if (form) {
            form.querySelector('input[name="_token"]').value = data.token;
            form.submit();
        }
    } catch (e) {
        window.location.href = '/logout';
    }
}
</script>
@yield('scripts')
</body>
</html>
