@extends('layouts.base')

@section('title', $jurnal->judul . ' — Jurnal Lampung Belajar')

@section('styles')
<link href="{{ asset('css/jurnal.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="container py-5" style="margin-top:70px">

  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Beranda</a></li>
      <li class="breadcrumb-item"><a href="/jurnal">Jurnal</a></li>
      <li class="breadcrumb-item active text-truncate" style="max-width:250px">{{ $jurnal->judul }}</li>
    </ol>
  </nav>

  <div class="row g-4">
    <div class="col-lg-9">
      <div class="jurnal-detail-box">
        <span class="jurnal-card-kategori">{{ $jurnal->kategori }}</span>
        <h1 class="mb-3" style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.8rem">
          {{ $jurnal->judul }}
        </h1>
        <div class="d-flex flex-wrap gap-4 text-muted small mb-4 pb-4 border-bottom">
          <span><i class="bi bi-person me-1"></i>{{ $jurnal->penulis }}</span>
          <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($jurnal->reviewed_at ?? $jurnal->created_at)->translatedFormat('d F Y') }}</span>
        </div>

        @if ($jurnal->abstrak)
        <h6 class="fw-bold mb-2">Abstrak</h6>
        <p class="text-muted" style="line-height:1.8">{{ $jurnal->abstrak }}</p>
        @endif

        @if ($jurnal->kata_kunci)
        <h6 class="fw-bold mb-2">Kata Kunci</h6>
        <p class="text-muted mb-4">{{ $jurnal->kata_kunci }}</p>
        @endif

        <a href="{{ asset('uploads/jurnal/' . basename($jurnal->file_jurnal)) }}" target="_blank"
           class="btn btn-lg mt-3 px-5" style="background:linear-gradient(135deg,#4a1a7a,#7b2fb5);color:#fff;border-radius:12px">
          <i class="bi bi-download me-2"></i>Unduh Jurnal
        </a>
      </div>
    </div>

    <div class="col-lg-3">
      <div class="jurnal-detail-box">
        <h6 class="fw-bold mb-3">Info Jurnal</h6>
        <div class="mb-3">
          <span class="text-muted small d-block">Kategori</span>
          <span class="fw-600">{{ $jurnal->kategori }}</span>
        </div>
        <div class="mb-3">
          <span class="text-muted small d-block">Penulis</span>
          <span class="fw-600">{{ $jurnal->penulis }}</span>
        </div>
        <div class="mb-3">
          <span class="text-muted small d-block">Jumlah Halaman</span>
          <span class="fw-600">{{ $jurnal->jumlah_halaman }} halaman</span>
        </div>
        @if ($jurnal->volume || $jurnal->nomor_edisi)
        <div class="mb-3">
          <span class="text-muted small d-block">Volume / Nomor</span>
          <span class="fw-600">{{ $jurnal->volume ?? '-' }}{{ $jurnal->nomor_edisi ? ' / '.$jurnal->nomor_edisi : '' }}</span>
        </div>
        @endif
        @if ($jurnal->issn)
        <div class="mb-3">
          <span class="text-muted small d-block">ISSN</span>
          <span class="fw-600">{{ $jurnal->issn }}</span>
        </div>
        @endif
        <div class="mb-3">
          <span class="text-muted small d-block">Bahasa</span>
          <span class="fw-600">{{ $jurnal->bahasa }}</span>
        </div>
        <div>
          <span class="text-muted small d-block">Dipublikasikan</span>
          <span class="fw-600">{{ \Carbon\Carbon::parse($jurnal->reviewed_at ?? $jurnal->created_at)->translatedFormat('d F Y') }} &middot; {{ $jurnal->tahun_terbit }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
