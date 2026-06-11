@extends('layouts.base')

@section('title', $materi->judul . ' — EduLampung')

@section('styles')
<link href="{{ asset('css/media.css') }}" rel="stylesheet">
<link href="{{ asset('css/detail.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="detail-wrapper">
  <div class="container py-4">

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Beranda</a></li>
        <li class="breadcrumb-item"><a href="/media">Media</a></li>
        <li class="breadcrumb-item"><a href="/media/{{ $jenjang }}">{{ strtoupper($materi->jenjang) }}</a></li>
        <li class="breadcrumb-item active text-truncate" style="max-width:200px">{{ $materi->judul }}</li>
      </ol>
    </nav>

    <div class="row g-4">

      <!-- KIRI: PLAYER -->
      <div class="col-lg-8">

        <!-- PLAYER AREA -->
        <div class="player-box">
          @if ($materi->tipe == 'video')
            <div class="ratio ratio-16x9">
              <iframe
                id="videoFrame"
                src="{{ youtubeEmbed($materi->url) }}"
                title="{{ $materi->judul }}"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
              </iframe>
            </div>

          @elseif ($materi->tipe == 'ppt')
          <div class="drive-viewer">
            <div class="drive-toolbar">
              <span class="drive-type-badge">
                <i class="bi bi-file-earmark-slides me-1"></i>Slide PPT
              </span>
              <a href="{{ $materi->url }}" target="_blank" rel="noopener" class="btn btn-outline-custom btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i>Buka di Drive
              </a>
            </div>

            <!-- Embed untuk desktop -->
            <div class="d-none d-md-block">
              <iframe
                src="{{ driveEmbed($materi->url, 'ppt') }}"
                frameborder="0"
                allowfullscreen
                id="driveFrame">
              </iframe>
              <div class="drive-loading" id="driveLoading">
                <div class="spinner-border text-success"></div>
                <p class="mt-2 text-muted small">Memuat dokumen...</p>
                <a href="{{ $materi->url }}" target="_blank" rel="noopener"
                  class="btn btn-outline-custom btn-sm mt-2" id="fallbackBtn" style="display:none">
                  <i class="bi bi-box-arrow-up-right me-1"></i>Buka Langsung di Drive
                </a>
              </div>
            </div>

            <!-- Fallback untuk mobile -->
            <div class="d-md-none mobile-fallback">
              <div class="text-center py-5 px-3">
                <div style="font-size:3rem">📑</div>
                <h6 class="mt-3 fw-700" style="font-family:'Sora',sans-serif">Slide PPT</h6>
                <p class="text-muted small mb-4">
                  Untuk pengalaman terbaik, buka slide ini di Google Drive.
                </p>
                <a href="{{ $materi->url }}" target="_blank" rel="noopener"
                  class="btn btn-primary-custom px-5">
                  <i class="bi bi-box-arrow-up-right me-2"></i>Buka Slide di Drive
                </a>
              </div>
            </div>
          </div>
          @endif
        </div>

        <!-- INFO MATERI -->
        <div class="materi-info mt-4">
          <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge rounded-pill badge-{{ $jenjang }}">{{ strtoupper($materi->jenjang) }}</span>
            <span class="badge rounded-pill badge-tipe-{{ $materi->tipe }}">
              @if ($materi->tipe == 'video') 🎬
              @elseif ($materi->tipe == 'ppt') 📑
              @else 📄
              @endif
              {{ strtoupper($materi->tipe) }}
            </span>
            <span class="badge rounded-pill bg-light text-muted border">{{ $materi->mapel->nama }}</span>
          </div>

          <h1 class="detail-title">{{ $materi->judul }}</h1>

          @if ($materi->deskripsi)
          <p class="detail-desc">{{ $materi->deskripsi }}</p>
          @endif

          <div class="detail-meta d-flex flex-wrap gap-3 mt-3">
            <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($materi->created_at)->format('Y-m-d') }}</span>
            <span><i class="bi bi-book me-1"></i>{{ $materi->mapel->nama }}</span>
            <span><i class="bi bi-mortarboard me-1"></i>{{ strtoupper($materi->jenjang) }}</span>
          </div>
        </div>

      </div>

      <!-- KANAN: MATERI TERKAIT -->
      <div class="col-lg-4">
        <div class="related-box">
          <h6 class="related-title">
            <i class="bi bi-collection me-2"></i>Materi Terkait
          </h6>
          <div id="relatedList">
            <div class="text-center py-4">
              <div class="spinner-border spinner-border-sm text-success"></div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  const MATERI_ID = {{ $materi->id }};
  const MAPEL     = "{{ $materi->mapel->nama }}";
  const JENJANG   = "{{ $jenjang }}";
</script>
<script src="{{ asset('js/detail.js') }}"></script>
@endsection
