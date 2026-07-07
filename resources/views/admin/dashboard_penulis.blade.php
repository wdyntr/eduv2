@extends('admin.layouts.base')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

<div class="mb-4">
  <h5 class="fw-bold mb-1" style="font-family:'Sora',sans-serif">Halo 👋</h5>
  <p class="text-muted mb-0">Berikut ringkasan pengajuan jurnal kamu.</p>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-journal-text" style="color:#2d5090"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statTotalUpload">-</div>
        <div class="stat-label">Total Diajukan</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-check-circle text-success"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statApproved">-</div>
        <div class="stat-label">Dipublikasikan</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon gold"><i class="bi bi-hourglass-split" style="color:#c47a00"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statPending">-</div>
        <div class="stat-label">Menunggu Review</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon red"><i class="bi bi-x-circle" style="color:#dc3545"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statRejected">-</div>
        <div class="stat-label">Ditolak</div>
      </div>
    </div>
  </div>
</div>

<!-- PERLU REVISI -->
<div class="admin-card mb-4" id="boxPerluRevisi" style="display:none">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Perlu Revisi</span>
    <a href="/admin/jurnal" class="btn btn-admin-edit btn-sm">Kelola Jurnal Saya</a>
  </div>
  <div id="listPerluRevisi" class="p-3"></div>
</div>

<!-- AKTIVITAS TERBARU -->
<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-clock-history me-2"></i>Pengajuan Terbaru</span>
    <a href="/admin/jurnal" class="btn btn-admin-primary btn-sm">
      <i class="bi bi-plus-lg me-1"></i>Ajukan Jurnal
    </a>
  </div>
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Judul</th>
          <th>Kategori</th>
          <th>Status</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody id="tabelAktivitasTerbaru">
        <tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>
      </tbody>
    </table>
  </div>
</div>

@endsection

@section('scripts')
<script>
  const JURNAL_ROLE = 'penulis';
</script>
<script src="{{ asset('js/admin_jurnal.js') }}"></script>
@endsection
