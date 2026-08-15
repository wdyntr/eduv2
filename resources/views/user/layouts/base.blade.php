<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin Lampung Belajar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @yield('styles')
    <script>
        // Login sekarang pakai Auth::attempt() + session Laravel, jadi semua
        // request POST/PUT/DELETE ke /api/* butuh header CSRF. Daripada
        // menambal satu-satu di setiap file admin_*.js, fetch() dibungkus di
        // sini supaya seluruh halaman panel otomatis terkirim CSRF-nya.
        (function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const originalFetch = window.fetch;
            window.fetch = function(input, init = {}) {
                const method = (init.method || 'GET').toUpperCase();
                if (csrfToken && method !== 'GET' && method !== 'HEAD') {
                    init.headers = {
                        ...(init.headers || {}),
                        'X-CSRF-TOKEN': csrfToken
                    };
                }
                return originalFetch(input, init);
            };
        })();
    </script>
</head>

<body class="admin-body">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="/admin">
                <span class="brand-edu">Lampung</span><span class="brand-lampung">Belajar</span>
            </a>

        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu Utama</div>

            <a href="/admin" class="sidebar-link {{ ($active_menu ?? '') == 'dashboard' ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            @can('materi.kelola')
                <a href="/admin/materi" class="sidebar-link {{ $active_menu == 'materi' ? 'active' : '' }}">
                    <i class="bi bi-collection-play"></i>
                    <span>Kelola Materi</span>
                </a>
            @endcan

            @canany(['sistem.kelola', 'classroom.kelola'])
                <a href="/admin/classroom"
                class="sidebar-link {{ $active_menu == 'classroom' ? 'active' : '' }}">
                    <i class="bi bi-building"></i>

                    <span>
                        @can('classroom.kelola')
                            {{ auth()->user()->sekolah_id
                                ? 'Classroom Saya'
                                : 'Monitoring Classroom' }}
                        @else
                            Kelola Sekolah
                        @endcan
                    </span>
                </a>
            @endcanany

            @can('materi.kelola')
                <a href="/admin/mapel" class="sidebar-link {{ $active_menu == 'mapel' ? 'active' : '' }}">
                    <i class="bi bi-book"></i>
                    <span>Mata Pelajaran</span>
                </a>
            @endcan

            @if (auth()->user()?->can('jurnal.review') || auth()->user()?->can('jurnal.ajukan') || auth()->user()?->can('jurnal.lihat'))
                <a href="/admin/jurnal" class="sidebar-link {{ $active_menu == 'jurnal' ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span>{{ auth()->user()->can('jurnal.ajukan') ? 'Jurnal Saya' : 'Request Jurnal' }}</span>
                </a>
            @endif

            <a href="/admin/profile" class="sidebar-link {{ $active_menu == 'profile' ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i>
                <span>Profil Saya</span>
            </a>

            @can('users.kelola')
                <div class="nav-section-label mt-3">Pengaturan</div>

                <a href="/admin/users"
                class="sidebar-link {{ $active_menu == 'users' ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Kelola User</span>
                </a>
            @endcan

            @can('sistem.kelola')
                <a href="/admin/roles"
                class="sidebar-link {{ $active_menu == 'roles' ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i>
                    <span>Kelola Role</span>
                </a>
            @endcan

            <a href="/" target="_blank" class="sidebar-link">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Lihat Website</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-info">
                <div class="admin-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="admin-detail">
                    <span class="admin-name">{{ $session_user }}</span>
                    <span
                        class="admin-role">{{ str($session_role ?? '')->replace('_', ' ')->headline() ?: 'User' }}</span>
                </div>
            </div>
            <form method="POST" action="/admin/logout">
                @csrf
                <button type="submit" class="btn-logout" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- TOPBAR -->
    <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-title">@yield('page_title', 'Dashboard')</div>
        <div class="topbar-right">
            <span
                class="sidebar-badge">{{ str($session_role ?? '')->replace('_', ' ')->headline() ?: 'User' }}</span>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="admin-main">
        @if (session('flash'))
            <div class="alert alert-{{ session('flash')['type'] }} alert-dismissible fade show mb-4" role="alert">
                <i
                    class="bi bi-{{ session('flash')['type'] == 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                {{ session('flash')['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.getElementById('adminSidebar').classList.toggle('open');
        });
    </script>
    @yield('scripts')

</body>

</html>