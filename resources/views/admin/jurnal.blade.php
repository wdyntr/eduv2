@extends('admin.layouts.base')

@section('title', 'Jurnal')
@section('page_title', ($session_role ?? 'admin') === 'admin' ? 'Request Upload Jurnal' : 'Jurnal Saya')

@section('content')

@if (($session_role ?? 'admin') === 'penulis')

  {{-- ================= TAMPILAN PENULIS ================= --}}
  <div class="admin-card">
    <div class="admin-card-header">
      <span class="admin-card-title"><i class="bi bi-journal-text me-2"></i>Riwayat Pengajuan Jurnal</span>
      <button class="btn-admin-primary btn" onclick="showFormJurnal()">
        <i class="bi bi-plus-lg me-1"></i>Ajukan Jurnal Baru
      </button>
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
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="tabelJurnalSaya">
          <tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>

@else

  {{-- ================= TAMPILAN ADMIN ================= --}}
  <div class="admin-card">
    <ul class="nav nav-tabs-admin" id="jurnalTabs">
      <li class="nav-item">
        <button class="nav-link active" data-tab="pending" onclick="switchJurnalTab('pending', this)">
          Menunggu Review <span class="badge bg-warning text-dark ms-1" id="countPending">0</span>
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-tab="all" onclick="switchJurnalTab('all', this)">
          Semua Jurnal
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-tab="kategori" onclick="switchJurnalTab('kategori', this)">
          Kelola Kategori
        </button>
      </li>
    </ul>

    <div id="tabPending">
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Judul</th>
              <th>Kategori</th>
              <th>Pengaju</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="tabelJurnalPending">
            <tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div id="tabAll" style="display:none">
      <div class="p-3 border-bottom">
        <select id="filterStatusJurnal" class="form-select" style="max-width:200px" onchange="loadJurnalAll()">
          <option value="">Semua Status</option>
          <option value="pending">Menunggu</option>
          <option value="approved">Disetujui</option>
          <option value="rejected">Ditolak</option>
        </select>
      </div>
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Judul</th>
              <th>Kategori</th>
              <th>Pengaju</th>
              <th>Status</th>
              <th>Reviewer</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="tabelJurnalAll">
            <tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div id="tabKategori" style="display:none">
      <div class="p-3 border-bottom">
        <div class="d-flex gap-2" style="max-width:400px">
          <input type="text" id="fNamaKategoriBaru" class="form-control" placeholder="Nama kategori baru, mis. Kesehatan">
          <button class="btn-admin-primary btn text-nowrap" onclick="tambahKategoriJurnal()">
            <i class="bi bi-plus-lg me-1"></i>Tambah
          </button>
        </div>
        <div id="kategoriAlert" class="alert d-none mt-2 py-2 small"></div>
      </div>
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Nama Kategori</th>
              <th>Jumlah Jurnal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="tabelKategoriJurnal">
            <tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- MODAL DETAIL / REVIEW -->
  <div class="modal fade" id="modalReviewJurnal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="border-radius:16px;border:none">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold" style="font-family:'Sora',sans-serif">Detail Pengajuan Jurnal</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="reviewJurnalBody"></div>
        <div class="modal-footer border-0 pt-0" id="reviewJurnalFooter"></div>
      </div>
    </div>
  </div>

  <!-- MODAL TOLAK (dengan catatan) -->
  <div class="modal fade" id="modalTolakJurnal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:16px;border:none">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold" style="font-family:'Sora',sans-serif">Tolak Pengajuan</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="form-label small fw-semibold">Catatan untuk penulis <span class="text-danger">*</span></label>
          <textarea id="fCatatanTolak" class="form-control" rows="4" placeholder="Jelaskan apa yang perlu diperbaiki..."></textarea>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px">Batal</button>
          <button class="btn btn-admin-danger" onclick="submitTolakJurnal()"><i class="bi bi-x-lg me-1"></i>Tolak</button>
        </div>
      </div>
    </div>
  </div>

@endif

{{-- ================= MODAL FORM (dipakai penulis) ================= --}}
<div class="modal fade" id="modalFormJurnal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:16px;border:none">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold" style="font-family:'Sora',sans-serif" id="formJurnalTitle">Ajukan Jurnal Baru</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="jurnalAlert" class="alert d-none mb-3"></div>
        <div id="jurnalCatatanBox" class="jurnal-note-box mb-3 d-none"></div>

        <div class="row g-3">
          <div class="col-12">
            <label class="form-label small fw-semibold">Judul Jurnal <span class="text-danger">*</span></label>
            <input type="text" id="fJudulJurnal" class="form-control" placeholder="Judul lengkap jurnal">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Kategori <span class="text-danger">*</span></label>
            <input type="text" id="fKategoriJurnal" class="form-control" list="kategoriJurnalOptions"
              placeholder="Pilih kategori dari daftar" autocomplete="off"
              onblur="validasiKategoriJurnal()">
            <datalist id="kategoriJurnalOptions"></datalist>
            <div class="invalid-feedback d-block d-none small" id="errKategoriJurnal">Kategori tidak ditemukan, pilih dari daftar yang tersedia.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Nama Penulis <span class="text-danger">*</span></label>
            <input type="text" id="fPenulisJurnal" class="form-control" placeholder="Nama penulis (bisa lebih dari satu)">
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Abstrak</label>
            <textarea id="fAbstrakJurnal" class="form-control" rows="3" placeholder="Ringkasan singkat isi jurnal"></textarea>
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Kata Kunci</label>
            <input type="text" id="fKataKunciJurnal" class="form-control" placeholder="Pisahkan dengan koma">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Jumlah Halaman <span class="text-danger">*</span></label>
            <input type="number" id="fJumlahHalamanJurnal" class="form-control" min="1" placeholder="Contoh: 12">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Tahun Terbit <span class="text-danger">*</span></label>
            <input type="number" id="fTahunTerbitJurnal" class="form-control" min="1990" placeholder="Contoh: 2026">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Bahasa</label>
            <select id="fBahasaJurnal" class="form-select">
              <option value="Indonesia">Indonesia</option>
              <option value="Inggris">Inggris</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">File Jurnal (PDF/DOC) <span class="text-danger" id="reqFileJurnal">*</span></label>
            <div class="dropzone-jurnal" onclick="document.getElementById('fFileJurnal').click()" id="dzFileJurnal">
              <i class="bi bi-file-earmark-arrow-up" style="font-size:1.5rem"></i>
              <div class="small mt-1" id="dzFileJurnalLabel">Klik untuk pilih file (maks. 10MB)</div>
            </div>
            <input type="file" id="fFileJurnal" class="d-none" accept=".pdf,.doc,.docx"
              onchange="updateDzLabel('fFileJurnal','dzFileJurnalLabel','dzFileJurnal')">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Bukti Screenshot Plagiarisme <span class="text-danger" id="reqFileBukti">*</span></label>
            <div class="dropzone-jurnal" onclick="document.getElementById('fFileBukti').click()" id="dzFileBukti">
              <i class="bi bi-shield-check" style="font-size:1.5rem"></i>
              <div class="small mt-1" id="dzFileBuktiLabel">Klik untuk pilih file (maks. 5MB)</div>
            </div>
            <input type="file" id="fFileBukti" class="d-none" accept=".pdf,.jpg,.jpeg,.png"
              onchange="updateDzLabel('fFileBukti','dzFileBuktiLabel','dzFileBukti')">
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px">Batal</button>
        <button class="btn btn-admin-primary" onclick="submitFormJurnal()">
          <i class="bi bi-check-lg me-1"></i>Kirim Pengajuan
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  const JURNAL_ROLE = '{{ $session_role ?? "admin" }}';
</script>
<script src="{{ asset('js/admin_jurnal.js') }}"></script>
@endsection
