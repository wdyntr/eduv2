@extends('admin.layouts.base')

@section('title', 'Kelola Classroom')
@section('page_title', 'Kelola Classroom')

@section('content')

<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-building me-2"></i>Daftar Sekolah</span>
    <button class="btn-admin-primary btn" onclick="showFormTambah()">
      <i class="bi bi-plus-lg me-1"></i>Tambah Sekolah
    </button>
  </div>

  <div class="p-3 border-bottom">
    <div class="admin-filter-bar">
      <div class="input-group" style="max-width:260px">
        <span class="input-group-text bg-white border-end-0">
          <i class="bi bi-search text-muted" style="font-size:0.85rem"></i>
        </span>
        <input type="text" id="searchSekolah" class="form-control border-start-0"
          placeholder="Cari nama sekolah..." oninput="loadSekolahAdmin()">
      </div>
      <select id="filterJenjangAdmin" class="form-select" style="max-width:160px" onchange="loadSekolahAdmin()">
        <option value="">Semua Jenjang</option>
        <option value="sma">🎓 SMA</option>
        <option value="smk">🔧 SMK</option>
        <option value="slb">🌟 SLB</option>
      </select>
      <button class="btn btn-outline-secondary btn-sm" onclick="resetFilterSekolah()" style="border-radius:10px">
        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
      </button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Sekolah</th>
          <th>Jenjang</th>
          <th>Kota/Kabupaten</th>
          <th>Link Classroom</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelSekolah">
        <tr>
          <td colspan="6" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-success"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="p-3" id="paginasiSekolah"></div>
</div>

<!-- MODAL FORM -->
<div class="modal fade" id="modalSekolah" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px; border:none">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-700" id="modalSekolahTitle" style="font-family:'Sora',sans-serif">Tambah Sekolah</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="sekolahAlert" class="alert d-none mb-3"></div>
        <input type="hidden" id="fSekolahId">
        <div class="mb-3">
          <label class="form-label small fw-600">Nama Sekolah <span class="text-danger">*</span></label>
          <input type="text" id="fNamaSekolah" class="form-control" placeholder="Contoh: SMAN 1 Bandar Lampung">
        </div>
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label small fw-600">Jenjang <span class="text-danger">*</span></label>
            <select id="fJenjangSekolah" class="form-select">
              <option value="">Pilih</option>
              <option value="sma">SMA</option>
              <option value="smk">SMK</option>
              <option value="slb">SLB</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small fw-600">Kota/Kabupaten</label>
            <input type="text" id="fKotaSekolah" class="form-control" placeholder="Bandar Lampung">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-600">Link Classroom</label>
          <input type="url" id="fUrlSekolah" class="form-control"
            placeholder="https://classroom.google.com/...">
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px">Batal</button>
        <button class="btn btn-admin-primary" onclick="submitSekolah()">
          <i class="bi bi-check-lg me-1"></i>Simpan
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
<script src="{{ asset('js/admin_classroom.js') }}"></script>
<script>document.addEventListener('DOMContentLoaded', loadSekolahAdmin);</script>
@endsection