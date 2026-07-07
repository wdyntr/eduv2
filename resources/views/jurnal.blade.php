@extends('layouts.base')

@section('title', 'Jurnal')

@section('styles')
<link href="{{ asset('css/jurnal.css') }}" rel="stylesheet">
@endsection

@section('content')

<!-- HERO -->
<section class="jurnal-hero">
  <div class="container">
    <div class="row align-items-center py-4">
      <div class="col-lg-8">
        <span class="hero-badge mb-3 d-inline-flex">
          <span class="badge-dot"></span> Jurnal Ilmiah
        </span>
        <h1 class="hero-title mb-3">
          Publikasi <span class="text-highlight">Jurnal</span><br>Pendidikan Lampung
        </h1>
        <p class="hero-desc mb-4">
          Kumpulan jurnal ilmiah dari para pendidik dan penulis se-Provinsi Lampung
          yang telah direview dan dipublikasikan.
        </p>
        <div class="media-search">
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0">
              <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" id="searchJurnal" class="form-control border-start-0 ps-0"
              placeholder="Cari judul atau nama penulis..." oninput="debounceSearchJurnal()">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FILTER KATEGORI -->
<section class="py-4 border-bottom">
  <div class="container">
    <div class="d-flex gap-2 flex-nowrap overflow-auto" id="kategoriChips">
      <button class="kategori-chip active" data-kategori="" onclick="filterKategori('', this)">Semua Kategori</button>
    </div>
  </div>
</section>

<!-- GRID JURNAL -->
<section class="py-5 bg-light-custom">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <p class="mb-0 text-muted small" id="jurnalResultCount">Memuat...</p>
    </div>

    <div class="row g-4" id="jurnalGrid">
      <div class="col-12 text-center py-5">
        <div class="spinner-border" style="color:#7b2fb5"></div>
        <p class="mt-2 text-muted">Memuat data jurnal...</p>
      </div>
    </div>

    <div class="mt-4" id="jurnalPaginasi"></div>
  </div>
</section>

@endsection

@section('scripts')
<script src="{{ asset('js/jurnal.js') }}"></script>
@endsection
