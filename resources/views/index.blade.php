@extends('layouts.base')

@section('title', 'Beranda')

@section('styles')
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endsection

@section('body_class', 'page-home')

@section('content')

<!-- HERO -->
<section class="hero-section" id="hero">
  <div class="siger-decoration">
    <svg viewBox="0 0 400 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
      <path d="M0,120 L40,40 L80,80 L120,20 L160,60 L200,0 L240,60 L280,20 L320,80 L360,40 L400,120 Z" fill="rgba(240,165,0,0.15)"/>
      <path d="M0,120 L40,60 L80,90 L120,40 L160,75 L200,20 L240,75 L280,40 L320,90 L360,60 L400,120 Z" fill="rgba(240,165,0,0.08)"/>
    </svg>
  </div>
  <div class="tumpal-right d-none d-lg-block">
    <svg viewBox="0 0 120 400" xmlns="http://www.w3.org/2000/svg">
      <polygon points="0,0 120,40 0,80" fill="rgba(26,58,107,0.2)"/>
      <polygon points="0,90 120,130 0,170" fill="rgba(26,58,107,0.15)"/>
      <polygon points="0,180 120,220 0,260" fill="rgba(26,58,107,0.1)"/>
      <polygon points="0,270 120,310 0,350" fill="rgba(26,58,107,0.07)"/>
    </svg>
  </div>

  <div class="container">
    <div class="row align-items-center min-vh-100 py-5">
      <div class="col-lg-7 hero-content">
        <div class="hero-badge mb-4">
          <span class="badge-dot"></span>
          Platform Belajar Digital Lampung
        </div>
        <h1 class="hero-title mb-4">
          Belajar Lebih<br>
          <span class="text-highlight">Cerdas</span> untuk<br>
          Lampung Maju
        </h1>
        <p class="hero-desc mb-5">
          Akses ribuan materi pembelajaran berkualitas untuk SMA, SMK, dan SLB
          se-Provinsi Lampung. Video, modul, dan kuis interaktif — gratis untuk semua pelajar.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <a href="/media" class="btn btn-primary-custom btn-lg px-5">
            <i class="bi bi-play-circle me-2"></i>Mulai Belajar
          </a>
        </div>
      </div>
      <div class="col-lg-5 d-none d-lg-flex justify-content-center">
        <div class="hero-visual">
          <img src="{{ asset('images/logoBalai.png') }}"
               alt="Siger Baltik Lampung"
               class="siger-illustration"
               style="max-width:380px; width:100%;">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MENU UTAMA -->
<section class="section-menu py-6">
  <div class="tapis-border top"></div>
  <div class="container py-5">
    <div class="text-center mb-5">
      <span class="section-label">Layanan Kami</span>
      <h2 class="section-title mt-2">Apa yang Ingin Kamu Akses?</h2>
      <p class="section-desc">Pilih layanan yang kamu butuhkan</p>
    </div>
    <div class="row g-4">

      <div class="col-6 col-lg-3">
        <a href="/media" class="menu-card menu-media">
          <div class="menu-icon">🎬</div>
          <h5>Media</h5>
          <p>Video & PPT pembelajaran SMA, SMK, SLB</p>
          <span class="menu-arrow"><i class="bi bi-arrow-right"></i></span>
        </a>
      </div>

      <div class="col-6 col-lg-3">
        <a href="/classroom" class="menu-card menu-classroom">
          <div class="menu-icon">🏫</div>
          <h5>Classroom</h5>
          <p>Daftar kelas online setiap sekolah di Lampung</p>
          <span class="menu-arrow"><i class="bi bi-arrow-right"></i></span>
        </a>
      </div>

      <div class="col-6 col-lg-3">
        <a href="{{ config('site.radio_url') }}" target="_blank" rel="noopener" class="menu-card menu-radio">
          <div class="menu-icon">📻</div>
          <h5>Radio</h5>
          <p>Dengarkan siaran radio pendidikan Lampung</p>
          <span class="menu-arrow"><i class="bi bi-box-arrow-up-right"></i></span>
        </a>
      </div>

      <div class="col-6 col-lg-3">
        <a href="{{ config('site.youtube_url') }}" target="_blank" rel="noopener" class="menu-card menu-youtube">
          <div class="menu-icon">▶️</div>
          <h5>YouTube</h5>
          <p>Channel YouTube resmi Balai TIK</p>
          <span class="menu-arrow"><i class="bi bi-box-arrow-up-right"></i></span>
        </a>
      </div>

      {{-- Aksi Jihan --}}
      <div class="col-6 col-lg-3">
        <a href="{{ config('site.aksijihan')  }}" target="_blank" rel="noopener" class="menu-card menu-aksijihan">
          <div class="menu-icon">
            <img src="{{ asset('images/aksiJihan.png') }}" alt="Aksi Jihan" style="height:48px;object-fit:contain;"> </div>
          <h5>Aksi Jihan</h5>
          <p>Platform pembelajaran interaktif</p>
          <span class="menu-arrow"><i class="bi bi-box-arrow-up-right"></i></span>
        </a>
      </div>

      {{-- RMD --}}
      <div class="col-6 col-lg-3">
        <a href="{{ config('site.rmd')  }}" target="_blank" rel="noopener" class="menu-card menu-rmd">
          <div class="menu-icon">
            <img src="{{ asset('images/rmd.webp') }}" alt="RMD" style="height:48px;object-fit:contain;">
          </div>
          <h5>RMD</h5>
          <p>Sumber referensi pendidikan digital</p>
          <span class="menu-arrow"><i class="bi bi-box-arrow-up-right"></i></span>
        </a>
      </div>

      {{-- PDF Pembelajaran --}}
      <div class="col-6 col-lg-3">
        <a href="{{ config('site.pdf')  }}" target="_blank" rel="noopener" class="menu-card menu-pdf">
          <div class="menu-icon">📄</div>
          <h5>PDF Pembelajaran</h5>
          <p>Kumpulan modul dan buku pelajaran PDF</p>
          <span class="menu-arrow"><i class="bi bi-box-arrow-up-right"></i></span>
        </a>
      </div>

      {{-- D Smart --}}
      <div class="col-6 col-lg-3">
        <a href="{{ config('site.dsmart')  }}" target="_blank" rel="noopener" class="menu-card menu-dsmart">
          <div class="menu-icon">
            <img src="{{ asset('images/dsmart.png') }}" alt="D Smart" style="height:48px;object-fit:contain;">
          </div>
          <h5>D Smart</h5>
          <p>Platform belajar cerdas</p>
          <span class="menu-arrow"><i class="bi bi-box-arrow-up-right"></i></span>
        </a>
      </div>

      {{-- Tes MBTI --}}
      <div class="col-6 col-lg-3">
        <a href="{{ config('site.mbti')  }}" target="_blank" rel="noopener" class="menu-card menu-mbti">
          <div class="menu-icon">🧠</div>
          <h5>Tes MBTI</h5>
          <p>Kenali kepribadianmu dengan tes MBTI gratis</p>
          <span class="menu-arrow"><i class="bi bi-box-arrow-up-right"></i></span>
        </a>
      </div>

    </div>
  </div>
  <div class="tapis-border bottom"></div>
</section>

<!-- MATERI TERBARU -->
<section class="section-materi py-6 bg-light-custom">
  <div class="container py-3">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-5">
      <div>
        <span class="section-label">Konten Terbaru</span>
        <h2 class="section-title mt-2">Materi Pilihan</h2>
      </div>
      <a href="/media" class="btn btn-outline-custom btn-sm px-4">
        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
    <div class="row g-4" id="materiBaru">
      <div class="col-12 text-center py-5">
        <div class="spinner-border text-success"></div>
      </div>
    </div>
  </div>
</section>

<!-- FITUR -->
<section class="section-fitur py-6">
  <div class="container py-3">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <span class="section-label">Kenapa Lampung Belajar?</span>
        <h2 class="section-title mt-2 mb-4">Belajar Lebih<br>Efektif & Menyenangkan</h2>
        <p class="text-muted">Platform kami dirancang khusus untuk kebutuhan pelajar Lampung dengan konten yang relevan dan mudah diakses.</p>
        <a href="/media" class="btn btn-primary-custom mt-4 px-4">
          Mulai Belajar <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
      <div class="col-lg-7">
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="fitur-card">
              <div class="fitur-icon bg-green-pale">🎬</div>
              <h5>Video Pembelajaran</h5>
              <p>Video berkualitas dari YouTube dengan penjelasan mudah dipahami.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="fitur-card">
              <div class="fitur-icon bg-blue-pale">📑</div>
              <h5>Slide & Presentasi</h5>
              <p>Akses slide PPT dari Google Drive tanpa perlu download.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="fitur-card">
              <div class="fitur-icon bg-gold-pale">📄</div>
              <h5>Journal </h5>
              <p>Baca modul dan buku pelajaran PDF langsung di browser.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="fitur-card">
              <div class="fitur-icon bg-red-pale">🏫</div>
              <h5>Classroom Online</h5>
              <p>Akses kelas online sekolahmu langsung dari satu platform.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- BUDAYA LAMPUNG -->
<section class="section-budaya py-6 bg-light-custom">
  <div class="tapis-border top"></div>
  <div class="container py-5">
    <div class="text-center mb-5">
      <span class="section-label">Kebanggaan Kita</span>
      <h2 class="section-title mt-2">Kaya Budaya, Kaya Ilmu</h2>
      <p class="section-desc">Lampung kaya akan warisan budaya yang menjadi inspirasi platform ini</p>
    </div>
    <div class="row g-4">
      <div class="col-6 col-md-3">
        <div class="budaya-card text-center">
          <div class="budaya-icon">👑</div>
          <h6>Siger</h6>
          <p>Mahkota kebesaran adat Lampung</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="budaya-card text-center">
          <div class="budaya-icon">🧵</div>
          <h6>Kain Tapis</h6>
          <p>Kain tenun khas Lampung</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="budaya-card text-center">
          <div class="budaya-icon">✍️</div>
          <h6>Aksara Lampung</h6>
          <p>Tulisan tradisional khas Lampung</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="budaya-card text-center">
          <div class="budaya-icon">🐘</div>
          <h6>Gajah Sumatera</h6>
          <p>Simbol Way Kambas Lampung</p>
        </div>
      </div>
    </div>
  </div>
  <div class="tapis-border bottom"></div>
</section>

@endsection

@section('scripts')
<script src="{{ asset('js/home.js') }}"></script>
@endsection
