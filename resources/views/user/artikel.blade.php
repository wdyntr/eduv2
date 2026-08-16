@extends('user.layouts.base')

@section('title', 'Kelola Artikel')
@section('page_title', 'Kelola Artikel')

@section('content')

<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-newspaper me-2"></i>Daftar Artikel</span>
    <a href="/admin/artikel/tambah" class="btn-admin-primary btn">
      <i class="bi bi-plus-lg me-1"></i>Tambah Artikel
    </a>
  </div>

  <div class="p-3 border-bottom">
    <div class="input-group" style="max-width:280px">
      <span class="input-group-text bg-white border-end-0">
        <i class="bi bi-search text-muted" style="font-size:0.85rem"></i>
      </span>
      <input type="text" id="searchArtikel" class="form-control border-start-0"
        placeholder="Cari judul artikel..." oninput="loadArtikelAdmin()">
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Judul</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelArtikelAdmin">
        <tr>
          <td colspan="5" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-success"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="p-3" id="paginasiArtikel"></div>
</div>

<div class="admin-card mt-4">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-tags me-2"></i>Kelola Kategori</span>
  </div>
  <div class="p-3 border-bottom">
    <div class="d-flex gap-2" style="max-width:500px">
      <input type="text" id="fNamaKategoriBaru" class="form-control" placeholder="Nama kategori, mis. Cerita Rakyat">
      <button class="btn-admin-primary btn text-nowrap" onclick="tambahKategoriArtikel()">
        <i class="bi bi-plus-lg me-1"></i>Tambah
      </button>
    </div>
    <div id="kategoriAlert" class="alert d-none mt-2 py-2 small"></div>
  </div>
  <div class="table-responsive">
    <table class="admin-table">
      <thead><tr><th>#</th><th>Nama</th><th>Jumlah Artikel</th><th>Aksi</th></tr></thead>
      <tbody id="tabelKategoriArtikel">
        <tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>
      </tbody>
    </table>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/admin_artikel.js') }}"></script>
@endsection