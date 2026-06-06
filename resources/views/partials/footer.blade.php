<footer class="footer-section">
  <div class="tapis-border top" style="opacity:0.3;"></div>
  <div class="container py-5">
    <div class="row g-4">

      <!-- Brand & desc -->
      <div class="col-lg-4">
        <a class="navbar-brand mb-3 d-block" href="/">
          <span class="brand-edu">Edu</span><span class="brand-lampung">Lampung</span>
        </a>
        <p class="text-white-50 small">
          Platform belajar digital untuk pelajar Lampung. Akses materi berkualitas dari mana saja, kapan saja.
        </p>
        <div class="d-flex gap-2 mt-3">
          <a href="{{ config('site.youtube_url') }}" target="_blank" class="social-link" title="YouTube">
            <i class="bi bi-youtube"></i>
          </a>
          <a href="{{ config('site.radio_url') }}" target="_blank" class="social-link" title="Radio">
            <i class="bi bi-broadcast"></i>
          </a>
        </div>
      </div>

      <!-- Menu Platform -->
      <div class="col-6 col-lg-2">
        <h6 class="footer-heading">Platform</h6>
        <ul class="footer-links">
          <li><a href="/media">Media</a></li>
          <li><a href="/classroom">Classroom</a></li>
          <li><a href="{{ config('site.radio_url') }}" target="_blank">Radio ↗</a></li>
          <li><a href="{{ config('site.youtube_url') }}" target="_blank">YouTube ↗</a></li>
        </ul>
      </div>

      <!-- Menu Media -->
      <div class="col-6 col-lg-2">
        <h6 class="footer-heading">Media</h6>
        <ul class="footer-links">
          <li><a href="/media/sma">SMA</a></li>
          <li><a href="/media/smk">SMK</a></li>
          <li><a href="/media/slb">SLB</a></li>
        </ul>
      </div>

      <!-- Admin -->
      <div class="col-6 col-lg-2">
        <h6 class="footer-heading">Admin</h6>
        <ul class="footer-links">
          <li><a href="#" data-bs-toggle="modal" data-bs-target="#modalLogin">Login Admin</a></li>
          <li><a href="/admin">Dashboard</a></li>
        </ul>
      </div>

    </div>

    <hr class="border-secondary mt-4 mb-3">
    <div class="d-flex justify-content-between flex-wrap gap-2">
      <p class="text-white-50 small mb-0">© 2026 EduLampung. Hak cipta dilindungi.</p>
      <p class="text-white-50 small mb-0">Dibuat dengan ❤️ untuk Lampung</p>
    </div>
  </div>
</footer>