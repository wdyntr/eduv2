@extends('user.layouts.base')

@section('title', 'Kelola User')
@section('page_title', 'Kelola User')

@section('content')

<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-people me-2"></i>Daftar User</span>
    <button class="btn-admin-primary btn" onclick="showFormTambahUser()">
      <i class="bi bi-plus-lg me-1"></i>Tambah User
    </button>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Username</th>
          <th>Role</th>
          <th>Bergabung</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelUser">
        <tr>
          <td colspan="6" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-success"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL TAMBAH USER -->
<div class="modal fade" id="modalUser" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold" style="font-family:'Sora',sans-serif">
          Tambah User
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="userAlert" class="alert d-none mb-3"></div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Nama Lengkap</label>
          <input type="text" id="fNamaUser" class="form-control" placeholder="Contoh: Budi Santoso">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
          <input type="text" id="fUsernameUser" class="form-control" placeholder="Contoh: budi123">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="password" id="fPasswordUser" class="form-control" placeholder="Minimal 6 karakter">
            <button class="btn btn-light border" type="button" onclick="togglePassUser()">
              <i class="bi bi-eye" id="eyeUserIcon"></i>
            </button>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Role <span class="text-danger">*</span></label>
          <select id="fRoleUser" class="form-select" onchange="toggleSekolahPicker()">
            <option value="admin">Admin</option>
            <option value="guru">Guru (Author Jurnal)</option>
            <option value="sekolah">Sekolah (Monitoring Classroom)</option>
          </select>
        </div>
        <div class="mb-3 d-none" id="wrapSekolahPicker">
          <label class="form-label small fw-semibold">Sekolah <span class="text-danger">*</span></label>
          <select id="fSekolahUser" class="form-select">
            <option value="">Memuat daftar sekolah...</option>
          </select>
          <div class="form-text">Akun ini hanya bisa mengelola data Classroom sekolah yang dipilih.</div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px">Batal</button>
        <button class="btn btn-admin-primary" onclick="submitUser()">
          <i class="bi bi-check-lg me-1"></i>Simpan
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  const CURRENT_USER_ID = {{ $session_id }};
</script>
<script src="{{ asset('js/admin.js') }}"></script>
<script src="{{ asset('js/admin_user.js') }}"></script>
@endsection