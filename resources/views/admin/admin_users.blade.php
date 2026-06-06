@extends('admin.layouts.base')

@section('title', 'Kelola Admin')
@section('page_title', 'Kelola Admin')

@section('content')

<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-people me-2"></i>Daftar Administrator</span>
    <button class="btn-admin-primary btn" onclick="showFormTambahAdmin()">
      <i class="bi bi-plus-lg me-1"></i>Tambah Admin
    </button>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Username</th>
          <th>Bergabung</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelAdmin">
        <tr>
          <td colspan="5" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-success"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL TAMBAH ADMIN -->
<div class="modal fade" id="modalAdmin" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold" style="font-family:'Sora',sans-serif">
          Tambah Administrator
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="adminAlert" class="alert d-none mb-3"></div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Nama Lengkap</label>
          <input type="text" id="fNamaAdmin" class="form-control" placeholder="Contoh: Budi Santoso">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
          <input type="text" id="fUsernameAdmin" class="form-control" placeholder="Contoh: budi123">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="password" id="fPasswordAdmin" class="form-control" placeholder="Minimal 6 karakter">
            <button class="btn btn-light border" type="button" onclick="togglePassAdmin()">
              <i class="bi bi-eye" id="eyeAdminIcon"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px">Batal</button>
        <button class="btn btn-admin-primary" onclick="submitAdmin()">
          <i class="bi bi-check-lg me-1"></i>Simpan
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  const CURRENT_ADMIN_ID = {{ $session_id }};
</script>
<script src="{{ asset('js/admin.js') }}"></script>
<script src="{{ asset('js/admin_user.js') }}"></script>
@endsection