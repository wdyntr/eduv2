<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard') — Admin EduLampung</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
  @yield('styles')
</head>
<body class="admin-body">

  <!-- SIDEBAR -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
      <a href="/admin">
        <span class="brand-edu">Edu</span><span class="brand-lampung">Lampung</span>
      </a>
      <span class="sidebar-badge">Admin</span>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-label">Menu Utama</div>

      <a href="/admin" class="sidebar-link {{ $active_menu == 'dashboard' ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </a>

      <a href="/admin/materi" class="sidebar-link {{ $active_menu == 'materi' ? 'active' : '' }}">
        <i class="bi bi-collection-play"></i>
        <span>Kelola Materi</span>
      </a>

      <a href="/admin/classroom" class="sidebar-link {{ $active_menu == 'classroom' ? 'active' : '' }}">
        <i class="bi bi-building"></i>
        <span>Kelola Classroom</span>
      </a>

      <a href="/admin/mapel" class="sidebar-link {{ $active_menu == 'mapel' ? 'active' : '' }}">
        <i class="bi bi-book"></i>
        <span>Mata Pelajaran</span>
      </a>

      <a href="/admin/profile" class="sidebar-link {{ $active_menu == 'profile' ? 'active' : '' }}">
        <i class="bi bi-person-gear"></i>
        <span>Profil Saya</span>
      </a>

      <div class="nav-section-label mt-3">Pengaturan</div>

      <a href="/admin/users" class="sidebar-link {{ $active_menu == 'users' ? 'active' : '' }}">
        <i class="bi bi-people"></i>
        <span>Kelola Admin</span>
      </a>

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
          <span class="admin-role">Administrator</span>
        </div>
      </div>
      <a href="/admin/logout" class="btn-logout" title="Logout">
        <i class="bi bi-box-arrow-right"></i>
      </a>
    </div>
  </aside>

  <!-- TOPBAR -->
  <div class="admin-topbar">
    <button class="sidebar-toggle" id="sidebarToggle">
      <i class="bi bi-list"></i>
    </button>
    <div class="topbar-title">@yield('page_title', 'Dashboard')</div>
    <div class="topbar-right">
      <span class="text-muted small">{{ $session_user }}</span>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <main class="admin-main">
    @if (session('flash'))
    <div class="alert alert-{{ session('flash')['type'] }} alert-dismissible fade show mb-4" role="alert">
      <i class="bi bi-{{ session('flash')['type'] == 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
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