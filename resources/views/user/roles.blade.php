@extends('user.layouts.base')

@section('title', 'Kelola Role')
@section('page_title', 'Kelola Role')

@section('content')
<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-shield-lock me-2"></i>Daftar Role</span>
    <button class="btn-admin-primary btn" onclick="showRoleForm()">
      <i class="bi bi-plus-lg me-1"></i>Tambah Role
    </button>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Role</th>
          <th>Permission</th>
          <th>Jumlah User</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelRole">
        <tr><td colspan="5" class="text-center py-4">
          <div class="spinner-border spinner-border-sm text-success"></div>
        </td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="modalRole" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold" id="modalRoleTitle" style="font-family:'Sora',sans-serif">Tambah Role</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="roleAlert" class="alert d-none mb-3"></div>
        <input type="hidden" id="fRoleId">

        <div class="mb-3">
          <label class="form-label small fw-semibold">Nama Role <span class="text-danger">*</span></label>
          <input type="text" id="fRoleName" class="form-control" maxlength="100"
                 placeholder="Contoh: operator_laporan">
          <div class="form-text">Gunakan nama teknis tanpa spasi, misalnya <code>operator_laporan</code>.</div>
        </div>

        <div>
          <label class="form-label small fw-semibold">Permission</label>
          <div id="permissionList" class="row g-2">
            <div class="text-muted small">Memuat permission...</div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px">Batal</button>
        <button class="btn btn-admin-primary" onclick="submitRole()">
          <i class="bi bi-check-lg me-1"></i>Simpan
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin_role.js') }}"></script>
@endsection
