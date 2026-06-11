@extends('layouts.base')

@section('title', 'Media Pembelajaran')

@section('styles')
<link href="{{ asset('css/media.css') }}" rel="stylesheet">
@endsection

@section('content')

<!-- HERO MEDIA -->
<section class="media-hero">
  <div class="tapis-border top" style="opacity:0.4;"></div>
  <div class="container">
    <div class="row align-items-center py-5">
      <div class="col-lg-7">
        <span class="hero-badge mb-3 d-inline-flex">
          <span class="badge-dot"></span> Media Pembelajaran
        </span>
        <h1 class="hero-title mb-3">
          Semua Materi<br>dalam Satu <span class="text-highlight">Tempat</span>
        </h1>
        <p class="hero-desc mb-4">
          Video YouTube dan slide PPT untuk SMA, SMK, dan SLB —
          dikelola oleh guru terpilih se-Provinsi Lampung.
        </p>
      </div>
      <div class="col-lg-5 d-none d-lg-block text-center pt-4">
        <div class="media-hero-stats">
          <div class="row g-3">
            <div class="col-6">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">🎬</div>
                <div class="hero-stat-num" id="countVideo">-</div>
                <div class="hero-stat-label">Video</div>
              </div>
            </div>
            <div class="col-6">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">📑</div>
                <div class="hero-stat-num" id="countPpt">-</div>
                <div class="hero-stat-label">Slide PPT</div>
              </div>
            </div>

            <div class="col-6">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">📚</div>
                <div class="hero-stat-num" id="countTotal">-</div>
                <div class="hero-stat-label">Total Materi</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="tapis-border bottom" style="opacity:0.4;"></div>
</section>

<!-- PILIH JENJANG -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="text-center mb-4">
      <span class="section-label">Jenjang Pendidikan</span>
      <h2 class="section-title mt-2">Pilih Jenjangmu</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <a href="/media/sma" class="jenjang-card card-sma">
          <div class="jenjang-icon">🎓</div>
          <div class="jenjang-tag">Jenjang</div>
          <h3>SMA</h3>
          <p>Kurikulum Merdeka & K13</p>
          <div class="jenjang-meta" id="meta-sma">
            <span><i class="bi bi-collection"></i> Memuat...</span>
          </div>
          <span class="jenjang-link mt-2">Lihat Materi <i class="bi bi-arrow-right"></i></span>
        </a>
      </div>
      <div class="col-md-4">
        <a href="/media/smk" class="jenjang-card card-smk">
          <div class="jenjang-icon">🔧</div>
          <div class="jenjang-tag">Jenjang</div>
          <h3>SMK</h3>
          <p>Berbagai Program Keahlian</p>
          <div class="jenjang-meta" id="meta-smk">
            <span><i class="bi bi-collection"></i> Memuat...</span>
          </div>
          <span class="jenjang-link mt-2">Lihat Materi <i class="bi bi-arrow-right"></i></span>
        </a>
      </div>
      <div class="col-md-4">
        <a href="/media/slb" class="jenjang-card card-slb">
          <div class="jenjang-icon">🌟</div>
          <div class="jenjang-tag">Jenjang</div>
          <h3>SLB</h3>
          <p>Pendidikan Inklusif & Khusus</p>
          <div class="jenjang-meta" id="meta-slb">
            <span><i class="bi bi-collection"></i> Memuat...</span>
          </div>
          <span class="jenjang-link mt-2">Lihat Materi <i class="bi bi-arrow-right"></i></span>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- SEMUA MATERI -->
<section class="py-5 bg-light-custom" id="semuaMateri">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
      <div>
        <span class="section-label">Konten Pembelajaran</span>
        <h2 class="section-title mt-1">Materi Terbaru</h2>
      </div>
      <div class="d-flex gap-2 flex-wrap align-items-center">
        <button class="btn btn-filter active" data-tipe="semua" onclick="filterTipe('semua', this)">Semua</button>
        <button class="btn btn-filter" data-tipe="video" onclick="filterTipe('video', this)">
          <i class="bi bi-play-circle me-1"></i>Video
        </button>
        <button class="btn btn-filter" data-tipe="ppt" onclick="filterTipe('ppt', this)">
          <i class="bi bi-file-earmark-slides me-1"></i>PPT
        </button>

        <div class="view-toggle ms-2">
          <button class="view-btn active" id="btnGrid" onclick="setView('grid')" title="Grid View">
            <i class="bi bi-grid-3x3-gap"></i>
          </button>
          <button class="view-btn" id="btnList" onclick="setView('list')" title="List View">
            <i class="bi bi-list-ul"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <p class="mb-0 text-muted small" id="resultCount">Memuat...</p>
      <select class="form-select form-select-sm w-auto" id="sortSelect" onchange="applyFilter()">
        <option value="terbaru">Terbaru</option>
        <option value="terlama">Terlama</option>
        <option value="az">A - Z</option>
        <option value="za">Z - A</option>
      </select>
    </div>

    <div id="materiGrid" class="view-grid">
      <div class="col-12 text-center py-5">
        <div class="spinner-border text-success"></div>
        <p class="mt-2 text-muted">Memuat materi...</p>
      </div>
    </div>

    <div class="text-center mt-4" id="loadMoreWrap" style="display:none">
      <button class="btn btn-outline-custom px-5" onclick="loadMore()">
        Muat Lebih Banyak <i class="bi bi-chevron-down ms-1"></i>
      </button>
    </div>
  </div>
</section>

@endsection

@section('scripts')
<script src="{{ asset('js/media.js') }}"></script>
@endsection
