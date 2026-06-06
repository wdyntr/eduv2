@extends('admin.layouts.base')

@section('title', 'Mata Pelajaran')
@section('page_title', 'Mata Pelajaran')

@section('content')

<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-book me-2"></i>Daftar Mata Pelajaran</span>
    <button class="btn-admin-primary btn" onclick="showFormTambahMapel()">
      <i class="bi bi-plus-lg me-1"></i>Tambah Mata Pelajaran
    </button>
  </div>

  <div class="p-3 border-bottom">
    <div class="admin-filter-bar">
      <div class="input-group" style="max-width:260px">
        <span class="input-group-text bg-white border-end-0">
          <i class="bi bi-search text-muted" style="font-size:0.85rem"></i>
        </span>
        <input type="text" id="searchMapel" class="form-control border-start-0"
          placeholder="Cari mata pelajaran..." oninput="loadMapelAdmin()">
      </div>
      <select id="filterJenjangMapel" class="form-select" style="max-width:160px" onchange="loadMapelAdmin()">
        <option value="">Semua Jenjang</option>
        <option value="sma">🎓 SMA</option>
        <option value="smk">🔧 SMK</option>
        <option value="slb">🌟 SLB</option>
      </select>
      <button class="btn btn-outline-secondary btn-sm" onclick="resetFilterMapel()" style="border-radius:10px">
        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
      </button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Mata Pelajaran</th>
          <th>Jenjang</th>
          <th>Jumlah Materi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelMapel">
        <tr>
          <td colspan="5" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-success"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="p-3" id="paginasiMapel"></div>
</div>

<!-- MODAL FORM -->
<div class="modal fade" id="modalMapel" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px; border:none">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-700" id="modalMapelTitle" style="font-family:'Sora',sans-serif">
          Tambah Mata Pelajaran
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="mapelAlert" class="alert d-none mb-3"></div>
        <input type="hidden" id="fMapelId">
        <div class="mb-3">
          <label class="form-label small fw-600">Nama Mata Pelajaran <span class="text-danger">*</span></label>
          <input type="text" id="fNamaMapel" class="form-control" placeholder="Contoh: Matematika">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-600">Jenjang <span class="text-danger">*</span></label>
          <select id="fJenjangMapel" class="form-select">
            <option value="">Pilih Jenjang</option>
            <option value="sma">🎓 SMA</option>
            <option value="smk">🔧 SMK</option>
            <option value="slb">🌟 SLB</option>
          </select>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px">Batal</button>
        <button class="btn btn-admin-primary" onclick="submitMapel()">
          <i class="bi bi-check-lg me-1"></i>Simpan
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
<script src="{{ asset('js/admin_mapel.js') }}"></script>
<script>document.addEventListener('DOMContentLoaded', loadMapelAdmin);</script>
@endsection