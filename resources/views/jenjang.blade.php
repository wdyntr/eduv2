@extends('layouts.base')

@section('title', $jenjang_nama . ' — Media')

@section('styles')
<link href="{{ asset('css/media.css') }}" rel="stylesheet">
@endsection

@section('content')

<!-- HEADER JENJANG -->
<section class="jenjang-header {{ $jenjang_class }}">
  <div class="container pt-5 pb-4" style="padding-top:100px!important">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb breadcrumb-light">
        <li class="breadcrumb-item"><a href="/">Beranda</a></li>
        <li class="breadcrumb-item"><a href="/media">Media</a></li>
        <li class="breadcrumb-item active">{{ $jenjang_nama }}</li>
      </ol>
    </nav>
    <div class="row align-items-center">
      <div class="col-lg-8">
        <div class="d-flex align-items-center gap-3 mb-3">
          <span class="jenjang-big-icon">{{ $jenjang_icon }}</span>
          <div>
            <span class="hero-badge d-inline-flex mb-1">
              <span class="badge-dot"></span> Jenjang Pendidikan
            </span>
            <h1 class="hero-title text-white mb-0">{{ $jenjang_nama }}</h1>
          </div>
        </div>
        <p class="text-white-50 mb-4">{{ $jenjang_desc }}</p>
        <div class="media-search" style="max-width:500px">
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0 border-0">
              <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0 border-0 ps-0"
              placeholder="Cari materi {{ $jenjang_nama }}...">
            <button class="btn btn-primary-custom px-4 border-0" onclick="doSearch()">
              <i class="bi bi-search me-1 d-none d-sm-inline"></i>Cari
            </button>
          </div>
        </div>
      </div>
      <div class="col-lg-4 d-none d-lg-flex justify-content-end">
        <div class="jenjang-stat-row">
          <div class="jenjang-stat">
            <span id="jenjangCountTotal">-</span>
            <small>Total Materi</small>
          </div>
          <div class="jenjang-stat">
            <span id="jenjangCountMapel">-</span>
            <small>Mata Pelajaran</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="tapis-border bottom" style="opacity:0.4;"></div>
</section>

<!-- FILTER & KONTEN -->
<section class="py-5 bg-light-custom">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-3">
        <div class="filter-sidebar">
          <h6 class="filter-title">
            <i class="bi bi-funnel me-2"></i>Filter
          </h6>
          <div class="filter-group">
            <label class="filter-label">Tipe Konten</label>
            <div class="d-flex flex-column gap-1">
              <label class="filter-check">
                <input type="radio" name="tipe" value="semua" checked onchange="applyFilter()"> Semua Tipe
              </label>
              <label class="filter-check">
                <input type="radio" name="tipe" value="video" onchange="applyFilter()">
                <i class="bi bi-play-circle text-success me-1"></i> Video YouTube
              </label>
              <label class="filter-check">
                <input type="radio" name="tipe" value="ppt" onchange="applyFilter()">
                <i class="bi bi-file-earmark-slides text-primary me-1"></i> Slide PPT
              </label>
              <label class="filter-check">
                <input type="radio" name="tipe" value="pdf" onchange="applyFilter()">
                <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Modul PDF
              </label>
            </div>
          </div>
          <div class="filter-group">
            <label class="filter-label">Mata Pelajaran</label>
            <div id="filterMapel" class="d-flex flex-column gap-1">
              <div class="spinner-border spinner-border-sm text-success"></div>
            </div>
          </div>
          <button class="btn btn-outline-custom btn-sm w-100 mt-2" onclick="resetFilter()">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
          </button>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
          <p class="mb-0 text-muted small" id="resultCount">Memuat...</p>
          <select class="form-select form-select-sm w-auto" id="sortSelect" onchange="applyFilter()">
            <option value="terbaru">Terbaru</option>
            <option value="terlama">Terlama</option>
            <option value="az">A - Z</option>
            <option value="za">Z - A</option>
          </select>
        </div>
        <div class="row g-3" id="materiGrid">
          <div class="col-12 text-center py-5">
            <div class="spinner-border text-success"></div>
            <p class="mt-2 text-muted">Memuat materi...</p>
          </div>
        </div>
        <nav class="mt-4" id="paginationWrap" style="display:none">
          <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>
      </div>
    </div>
  </div>
</section>

@endsection

@section('scripts')
<script>
  const JENJANG = "{{ $jenjang }}";
  const JENJANG_NAMA = "{{ $jenjang_nama }}";
</script>
<script src="{{ asset('js/media.js') }}"></script>
<script src="{{ asset('js/jenjang.js') }}"></script>
@endsection