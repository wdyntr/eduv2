<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Quiz Interaktif</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="login-bg"></div>

{{-- Theme toggle --}}
<button class="theme-toggle login-theme-btn" onclick="toggleTheme()"
        title="Ganti tema" aria-label="Toggle tema">
    <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
    </svg>
    <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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

<div class="login-wrapper">
    <div class="login-card">

        {{-- Header --}}
        <div class="login-header">
            <div class="login-logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
            </div>
            <h1 class="login-title">Quiz <em>Interaktif</em></h1>
            <p class="login-subtitle">Masuk untuk mengakses ujian</p>
        </div>

        {{-- Global error (misal akun tidak aktif) --}}
        @if($errors->has('username') && !$errors->has('password'))
            <div style="
                background: rgba(192,80,58,0.08);
                border: 1px solid var(--danger);
                border-radius: var(--radius);
                padding: 12px 16px;
                margin-bottom: 20px;
                font-size: 13px;
                color: #e08070;
                display: flex;
                align-items: center;
                gap: 8px;
            ">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" flex-shrink="0">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                {{ $errors->first('username') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login.post') }}" class="login-form">
            @csrf

            {{-- Username --}}
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username"
                        class="form-input {{ $errors->has('username') ? 'is-error' : '' }}"
                        autocomplete="username"
                        autofocus
                        required
                    >
                </div>
                @error('username')
                    <span class="form-error">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="toggle-password"
                            onclick="togglePasswordVisibility()"
                            aria-label="Tampilkan/sembunyikan password">
                        <svg id="eye-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="eye-off-icon" width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                             style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="form-error">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Ingat saya</label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-primary btn-login">
                Masuk
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
            </button>
        </form>

        <p class="login-footer">
            Hubungi administrator jika mengalami<br>masalah saat masuk.
        </p>
    </div>
</div>

<script>
    // Theme
    (function () {
        const saved     = localStorage.getItem('quiz-theme');
        const preferred = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
        document.documentElement.dataset.theme = saved ?? preferred;
    })();

    function toggleTheme() {
        const html = document.documentElement;
        const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
        html.dataset.theme = next;
        localStorage.setItem('quiz-theme', next);
    }

    // Toggle password
    function togglePasswordVisibility() {
        const input   = document.getElementById('password');
        const eyeOn   = document.getElementById('eye-icon');
        const eyeOff  = document.getElementById('eye-off-icon');
        const isHidden = input.type === 'password';

        input.type    = isHidden ? 'text' : 'password';
        eyeOn.style.display  = isHidden ? 'none'  : 'block';
        eyeOff.style.display = isHidden ? 'block' : 'none';
    }
</script>
</body>
</html>