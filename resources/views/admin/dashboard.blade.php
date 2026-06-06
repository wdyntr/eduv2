@extends('admin.layouts.base')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-collection-play text-success"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statMateri">-</div>
        <div class="stat-label">Total Materi</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-building" style="color:#2d5090"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statSekolah">-</div>
        <div class="stat-label">Total Sekolah</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon gold"><i class="bi bi-book" style="color:#c47a00"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statMapel">-</div>
        <div class="stat-label">Mata Pelajaran</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon red"><i class="bi bi-people" style="color:#dc3545"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statAdmin">-</div>
        <div class="stat-label">Admin</div>
      </div>
    </div>
  </div>
</div>

<!-- MATERI TERBARU -->
<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-clock-history me-2"></i>Materi Terbaru</span>
    <a href="/admin/materi/tambah" class="btn-admin-primary btn">
      <i class="bi bi-plus-lg me-1"></i>Tambah Materi
    </a>
  </div>
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Judul</th>
          <th>Jenjang</th>
          <th>Tipe</th>
          <th>Mata Pelajaran</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelMateri">
        <tr>
          <td colspan="6" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-success"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
@endsection