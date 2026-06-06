<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'EduLampung') — Platform Belajar Digital Lampung</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Global CSS -->
  <link href="{{ asset('css/main.css') }}" rel="stylesheet">

  <!-- Per-page CSS -->
  @yield('styles')
</head>
<body class="@yield('body_class')">

  <!-- NAVBAR -->
  @include('partials.navbar')

  <!-- KONTEN UTAMA -->
  <main>
    @yield('content')
  </main>

  <!-- FOOTER -->
  @include('partials.footer')

  <!-- MODAL LOGIN ADMIN -->
  <div class="modal fade" id="modalLogin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content login-modal">
        <div class="modal-header border-0 pb-0">
          <div>
            <h5 class="modal-title fw-800">
              <span class="brand-edu">Edu</span><span class="brand-lampung">Lampung</span>
            </h5>
            <p class="text-muted small mb-0">Login khusus administrator</p>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body pt-3">
          <div id="loginAlert" class="alert alert-danger d-none small py-2"></div>
          <div class="mb-3">
            <label class="form-label small fw-600">Username</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0">
                <i class="bi bi-person text-muted"></i>
              </span>
              <input type="text" id="loginUsername" class="form-control border-start-0"
                placeholder="Masukkan username">
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label small fw-600">Password</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0">
                <i class="bi bi-lock text-muted"></i>
              </span>
              <input type="password" id="loginPassword" class="form-control border-start-0 border-end-0"
                placeholder="Masukkan password">
              <button class="btn btn-light border border-start-0" type="button" id="eyeBtn">
                <i class="bi bi-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>
          <button class="btn btn-primary-custom w-100 py-2" onclick="doLogin()" id="loginBtn">
            <i class="bi bi-box-arrow-in-right me-2"></i>Login
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Global JS -->
  <script src="{{ asset('js/main.js') }}"></script>

  <!-- Per-page JS -->
  @yield('scripts')

</body>
</html>