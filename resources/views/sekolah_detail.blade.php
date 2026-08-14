@extends('layouts.base')

@section('title', $sekolah->nama . ' — Classroom')

@section('styles')
<link href="{{ asset('css/classroom.css') }}" rel="stylesheet">
@endsection

@section('content')

<section class="sekolah-detail-hero">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider-color:#ffffff88">
        <li class="breadcrumb-item"><a href="/classroom" class="text-white-50 text-decoration-none">Classroom</a></li>
        <li class="breadcrumb-item active text-white">{{ $sekolah->nama }}</li>
      </ol>
    </nav>
    <div class="d-flex align-items-center gap-3">
      <div class="hero-stat-icon" style="font-size:2.4rem">
        {{ [
                'sma' => '🎓',
                'smk' => '🔧',
                'slb' => '🌟'
            ][$sekolah->jenjang?->kode] ?? '🏫' }}
      </div>
      <div>
        <h1 class="hero-title mb-1" style="font-size:1.9rem">{{ $sekolah->nama }}</h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.7)">
          <span class="badge rounded-pill badge-{{ $sekolah->jenjang?->kode }} me-1">
              {{ strtoupper($sekolah->jenjang?->kode ?? '-') }}
          </span>
          @if ($sekolah->kotaKabupaten)
              <i class="bi bi-geo-alt me-1"></i>
              {{ $sekolah->kotaKabupaten->nama }}
          @endif
        </p>
      </div>
    </div>
  </div>
</section>

<section class="py-5 bg-light-custom">
  <div class="container">
    <h5 class="mb-1" style="font-family:'Sora',sans-serif;font-weight:700">Kelas per Mata Pelajaran</h5>
    <p class="text-muted small mb-4">Pilih mata pelajaran untuk masuk ke kelas Google Classroom-nya.</p>

    <div class="row g-3" id="mapelKelasGrid">
      <div class="col-12 text-center py-5">
        <div class="spinner-border text-success"></div>
      </div>
    </div>
  </div>
</section>

@endsection

@section('scripts')
<script>
  const SEKOLAH_ID_DETAIL = {{ $sekolah->id }};
</script>
<script src="{{ asset('js/sekolah_detail.js') }}"></script>
@endsection