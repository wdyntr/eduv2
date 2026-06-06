@extends('layouts.base')

@section('title', 'Classroom')

@section('styles')
<link href="{{ asset('css/classroom.css') }}" rel="stylesheet">
@endsection

@section('content')

<!-- HERO -->
<section class="classroom-hero">
  <div class="tapis-border top" style="opacity:0.4;"></div>
  <div class="container">
    <div class="row align-items-center py-5">
      <div class="col-lg-7">
        <span class="hero-badge mb-3 d-inline-flex">
          <span class="badge-dot"></span> Classroom Online
        </span>
        <h1 class="hero-title mb-3">
          Kelas Online<br>Sekolah <span class="text-highlight">se-Lampung</span>
        </h1>
        <p class="hero-desc mb-4">
          Temukan dan akses kelas online sekolahmu langsung dari satu platform.
          Tersedia untuk SMA, SMK, dan SLB se-Provinsi Lampung.
        </p>
        <div class="media-search">
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0">
              <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0 ps-0"
              placeholder="Cari nama sekolah...">
            <button class="btn btn-primary-custom px-4" onclick="doSearch()">Cari</button>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <span class="filter-tag active" onclick="filterJenjang('semua', this)">Semua</span>
          <span class="filter-tag" onclick="filterJenjang('sma', this)">SMA</span>
          <span class="filter-tag" onclick="filterJenjang('smk', this)">SMK</span>
          <span class="filter-tag" onclick="filterJenjang('slb', this)">SLB</span>
        </div>
      </div>
      <div class="col-lg-5 d-none d-lg-block text-center pt-4">
        <div class="classroom-hero-stats">
          <div class="row g-3">
            <div class="col-6">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">🎓</div>
                <div class="hero-stat-num" id="countSma">-</div>
                <div class="hero-stat-label">SMA</div>
              </div>
            </div>
            <div class="col-6">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">🔧</div>
                <div class="hero-stat-num" id="countSmk">-</div>
                <div class="hero-stat-label">SMK</div>
              </div>
            </div>
            <div class="col-6">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">🌟</div>
                <div class="hero-stat-num" id="countSlb">-</div>
                <div class="hero-stat-label">SLB</div>
              </div>
            </div>
            <div class="col-6">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">🏫</div>
                <div class="hero-stat-num" id="countTotal">-</div>
                <div class="hero-stat-label">Total Sekolah</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="tapis-border bottom" style="opacity:0.4;"></div>
</section>

<!-- DAFTAR SEKOLAH -->
<section class="py-5 bg-light-custom">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <p class="mb-0 text-muted small" id="resultCount">Memuat...</p>
      <select class="form-select form-select-sm w-auto" id="sortSelect" onchange="loadClassroom()">
        <option value="az">A - Z</option>
        <option value="za">Z - A</option>
      </select>
    </div>

    <div class="row g-4" id="classroomGrid">
      <div class="col-12 text-center py-5">
        <div class="spinner-border text-success"></div>
        <p class="mt-2 text-muted">Memuat data sekolah...</p>
      </div>
    </div>

    <div id="emptyState" class="empty-state d-none">
      <div class="empty-icon">🏫</div>
      <h5>Sekolah tidak ditemukan</h5>
      <p>Coba ubah kata kunci atau filter pencarian.</p>
    </div>
  </div>
</section>

@endsection

@section('scripts')
<script src="{{ asset('js/classroom.js') }}"></script>
@endsection