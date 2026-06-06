@extends('layouts.base')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div style="min-height:60vh; display:flex; align-items:center; justify-content:center; padding-top:80px">
  <div class="text-center">
    <div style="font-size:5rem">🔍</div>
    <h1 class="mt-3" style="font-family:'Sora',sans-serif; font-weight:800; font-size:2.5rem">404</h1>
    <p class="text-muted mb-4">Halaman yang kamu cari tidak ditemukan.</p>
    <a href="/" class="btn btn-primary-custom px-5">
      <i class="bi bi-house me-2"></i>Kembali ke Beranda
    </a>
  </div>
</div>
@endsection