@extends('layouts.base')

@section('title', $kategori_nama ?? 'Artikel Budaya Lampung')

@section('styles')
<link href="{{ asset('css/artikel.css') }}" rel="stylesheet">
@endsection

@section('content')

<section class="artikel-hero">
  <div class="tapis-border top" style="opacity:0.4;"></div>
  <div class="container py-4">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <span class="hero-badge mb-3 d-inline-flex">
          <span class="badge-dot"></span> Artikel Budaya
        </span>
        @if (isset($kategori_slug))
          <h1 class="hero-title mb-3">{{ $kategori_nama }}</h1>
        @else
          <h1 class="hero-title mb-3">
            Artikel <span class="text-highlight">Budaya</span><br>Lampung
          </h1>
        @endif
        <p class="hero-desc mb-0">
          Mengenal lebih dekat sejarah, adat, dan tradisi masyarakat Lampung
          melalui kumpulan artikel dan video budaya se-Provinsi Lampung.
        </p>
      </div>
    </div>
  </div>
  <div class="tapis-border bottom" style="opacity:0.4;"></div>
</section>

@unless(isset($kategori_slug))
<section class="py-5 bg-white">
  <div class="container">
    <h2 class="section-title mb-4">Kategori</h2>
    <div class="row g-3" id="kategoriGrid">
      <div class="col-12 text-center py-4"><div class="spinner-border text-success"></div></div>
    </div>
  </div>
</section>
@endunless

<section class="py-5 bg-light-custom">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      <h2 class="section-title mb-0">{{ isset($kategori_slug) ? 'Artikel ' . $kategori_nama : 'Semua Konten' }}</h2>
      <div class="d-flex gap-2">
        <button class="btn btn-filter active" data-tipe="" onclick="filterTipeArtikel('', this)">Semua</button>
        <button class="btn btn-filter" data-tipe="artikel" onclick="filterTipeArtikel('artikel', this)">
          <i class="bi bi-file-text me-1"></i>Artikel
        </button>
        <button class="btn btn-filter" data-tipe="video" onclick="filterTipeArtikel('video', this)">
          <i class="bi bi-play-circle me-1"></i>Video
        </button>
      </div>
    </div>

    <div id="artikelGrid" class="row g-3">
      <div class="col-12 text-center py-5"><div class="spinner-border text-success"></div></div>
    </div>
  </div>
</section>

@endsection

@section('scripts')
<script>
  const ARTIKEL_KATEGORI_SLUG = @json($kategori_slug ?? null);
</script>
<script src="{{ asset('js/artikel.js') }}"></script>
@endsection