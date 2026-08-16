@extends('layouts.base')

@section('title', $artikel->judul)

@section('styles')
<link href="{{ asset('css/artikel.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="container py-5">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Beranda</a></li>
      <li class="breadcrumb-item"><a href="/artikel">Artikel Budaya</a></li>
      @if ($artikel->kategori)
        <li class="breadcrumb-item"><a href="/artikel/kategori/{{ $artikel->kategori->slug }}">{{ $artikel->kategori->nama }}</a></li>
      @endif
      <li class="breadcrumb-item active text-truncate" style="max-width:220px">{{ $artikel->judul }}</li>
    </ol>
  </nav>

  <div class="row g-4">
    <div class="col-lg-8">
      @if ($artikel->tipe === 'video' && $artikel->video_url)
        <div class="ratio ratio-16x9 mb-4">
          <iframe src="{{ youtubeEmbed($artikel->video_url) }}" allowfullscreen></iframe>
        </div>
      @elseif ($artikel->thumbnail)
        <img src="{{ $artikel->thumbnail }}" class="img-fluid rounded mb-4" alt="{{ $artikel->judul }}">
      @endif

      <h1 class="detail-title">{{ $artikel->judul }}</h1>
      <div class="detail-meta mb-3">
        <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($artikel->created_at)->format('d M Y') }}</span>
        @if ($artikel->kategori)
          <span class="badge rounded-pill bg-light text-dark border ms-2">{{ $artikel->kategori->nama }}</span>
        @endif
      </div>

      <div class="artikel-konten">{!! nl2br(e($artikel->konten)) !!}</div>
    </div>
  </div>
</div>

@endsection