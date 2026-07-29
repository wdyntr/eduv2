@extends('admin.layouts.base')

@section('title', ($session_role ?? 'admin') === 'sekolah' ? 'Classroom Saya' : 'Monitoring Classroom')
@section('page_title', ($session_role ?? 'admin') === 'sekolah' ? 'Classroom Saya' : 'Kelola & Monitoring Classroom')

@section('content')

@if (($session_role ?? 'admin') === 'sekolah')

<!-- ===================== -->
<!-- TAMPILAN UNTUK ROLE: SEKOLAH -->
<!-- ===================== -->
<div class="row g-3">
  <div class="col-lg-7 mx-auto">
    <div class="admin-card">
      <div class="admin-card-header">
        <span class="admin-card-title"><i class="bi bi-building me-2"></i>Profil Sekolah</span>
      </div>
      <div class="p-3" id="profilSekolahBox">
        <div class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></div>
      </div>
    </div>

    <div class="admin-card mt-3">
      <div class="admin-card-header">
        <span class="admin-card-title"><i class="bi bi-collection-play me-2"></i>Kelas per Mata Pelajaran</span>
      </div>
      <div class="p-3">
        <p class="text-muted small mb-3">
          Setiap mata pelajaran punya kelas Classroom masing-masing. Kelola link-nya di halaman khusus.
        </p>
        <button class="btn btn-admin-edit w-100" onclick="location.href='/admin/classroom/{{ $session_sekolah_id }}'">
          <i class="bi bi-arrow-right me-1"></i>Kelola Kelas Mata Pelajaran
        </button>
      </div>
    </div>
  </div>
</div>

@else

<!-- ===================== -->
<!-- TAMPILAN UNTUK ROLE: ADMIN / DINAS -->
<!-- ===================== -->
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
          <th>Kelas Terisi</th>
          <th>Task Bulan Ini</th>
          <th>Materi Bulan Ini</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelSekolah">
        <tr>
          <td colspan="8" class="text-center py-4">
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
        <p class="text-muted small mb-0">
          <i class="bi bi-info-circle me-1"></i>Link Google Classroom per mata pelajaran dikelola lewat halaman
          <strong>Kelola Kelas</strong> (ikon <i class="bi bi-collection-play"></i>) setelah sekolah ini disimpan.
        </p>
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

@endif

@endsection

@section('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
<script src="{{ asset('js/admin_classroom.js') }}"></script>
<script>
  const CLASSROOM_ROLE = "{{ $session_role ?? 'admin' }}";
  document.addEventListener('DOMContentLoaded', () => {
    if (CLASSROOM_ROLE === 'sekolah') {
      loadProfilSekolah({{ $session_sekolah_id ?? 'null' }});
    } else {
      loadSekolahAdmin();
    }
  });
</script>
@endsection