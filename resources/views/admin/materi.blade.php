@extends('admin.layouts.base')

@section('title', 'Kelola Materi')
@section('page_title', 'Kelola Materi')

@section('content')

<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-collection-play me-2"></i>Daftar Materi</span>
    <a href="/admin/materi/tambah" class="btn-admin-primary btn">
      <i class="bi bi-plus-lg me-1"></i>Tambah Materi
    </a>
  </div>

  <!-- Filter -->
  <div class="p-3 border-bottom">
    <div class="admin-filter-bar">
      <div class="input-group" style="max-width:260px">
        <span class="input-group-text bg-white border-end-0">
          <i class="bi bi-search text-muted" style="font-size:0.85rem"></i>
        </span>
        <input type="text" id="searchMateri" class="form-control border-start-0"
          placeholder="Cari judul materi..." oninput="loadMateriAdmin()">
      </div>
      <select id="filterJenjang" class="form-select" style="max-width:160px" onchange="loadMateriAdmin()">
        <option value="">Semua Jenjang</option>
        <option value="sma">🎓 SMA</option>
        <option value="smk">🔧 SMK</option>
        <option value="slb">🌟 SLB</option>
      </select>
      <select id="filterTipe" class="form-select" style="max-width:160px" onchange="loadMateriAdmin()">
        <option value="">Semua Tipe</option>
        <option value="video">🎬 Video</option>
        <option value="ppt">📑 PPT</option>
        <option value="pdf">📄 PDF</option>
      </select>
      <button class="btn btn-outline-secondary btn-sm" onclick="resetFilterMateri()" style="border-radius:10px">
        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
      </button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Judul</th>
          <th>Jenjang</th>
          <th>Tipe</th>
          <th>Mata Pelajaran</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelMateriAdmin">
        <tr>
          <td colspan="7" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-success"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="p-3" id="paginasiMateri"></div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
<script>document.addEventListener('DOMContentLoaded', loadMateriAdmin);</script>
@endsection