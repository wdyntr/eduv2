<nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
  <div class="container">

    <!-- Brand -->
    <a class="navbar-brand" href="/">
      <span class="brand-edu">Lampung</span><span class="brand-lampung">Belajar</span>
    </a>

    <!-- Toggler mobile -->
    <button class="navbar-toggler border-0 shadow-none" type="button"
      data-bs-toggle="collapse" data-bs-target="#navMenu"
      aria-controls="navMenu" aria-expanded="false">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">

        <li class="nav-item">
          <a class="nav-link {{ ($active_page ?? '') == 'beranda' ? 'active' : '' }}" href="/">
            Beranda
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ ($active_page ?? '') == 'media' ? 'active' : '' }}" href="/media">
            <i class="bi bi-play-circle me-1"></i>Media
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ ($active_page ?? '') == 'classroom' ? 'active' : '' }}" href="/classroom">
            <i class="bi bi-building me-1"></i>Classroom
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="{{ config('site.radio_url') }}" target="_blank" rel="noopener">
            <i class="bi bi-broadcast me-1"></i>Radio
            <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.65rem;opacity:0.6;"></i>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="{{ config('site.youtube_url') }}" target="_blank" rel="noopener">
            <i class="bi bi-youtube me-1"></i>YouTube
            <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.65rem;opacity:0.6;"></i>
          </a>
        </li>

        <li class="nav-item ms-lg-2">
          <a class="btn btn-primary-custom btn-sm px-4" href="/media">
            Mulai Belajar
          </a>
        </li>

      </ul>
    </div>
  </div>
</nav>
