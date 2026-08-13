@extends('admin.layouts.base')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">

    <div class="admin-form-card">
      <h6 class="fw-bold mb-4" style="font-family:'Sora',sans-serif">
        <i class="bi bi-person-circle me-2 text-success"></i>Update Profil
      </h6>

      <div id="profileAlert" class="alert d-none mb-3"></div>

      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" class="form-control bg-light" value="{{ $session_user }}" disabled>
        <div class="form-text">Username tidak dapat diubah.</div>
      </div>

      <div class="mb-4">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" id="fNama" class="form-control" placeholder="Nama lengkap Anda">
      </div>

      <hr class="my-4">
      <h6 class="fw-bold mb-3 text-muted" style="font-size:0.85rem">
        <i class="bi bi-lock me-2"></i>Ganti Password <span class="fw-normal">(opsional)</span>
      </h6>

      <div class="mb-3">
        <label class="form-label">Password Lama</label>
        <input type="password" id="fPassLama" class="form-control" placeholder="Kosongkan jika tidak ingin ganti">
      </div>

      <div class="mb-4">
        <label class="form-label">Password Baru</label>
        <input type="password" id="fPassBaru" class="form-control" placeholder="Minimal 6 karakter">
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-admin-primary px-4" onclick="submitProfile()">
          <i class="bi bi-check-lg me-1"></i>Simpan
        </button>
        <a href="/admin" class="btn btn-outline-secondary px-4" style="border-radius:10px">Batal</a>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
const SESSION_USER = "{{ $session_user }}";

async function loadProfile() {
  try {
    const res  = await fetch('/api/admin/users');
    const data = await res.json();
    const me   = data.items?.find(a => a.username === SESSION_USER);
    if (me) document.getElementById('fNama').value = me.nama || '';
  } catch {}
}

async function submitProfile() {
  const nama         = document.getElementById('fNama').value.trim();
  const passwordLama = document.getElementById('fPassLama').value;
  const passwordBaru = document.getElementById('fPassBaru').value;

  try {
    const res = await fetch('/api/admin/profile', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        nama,
        password_lama: passwordLama,
        password_baru: passwordBaru,
      }),
    });

    if (res.ok) {
      showAlert('success', 'Profil berhasil diperbarui!');
      document.getElementById('fPassLama').value = '';
      document.getElementById('fPassBaru').value = '';
    } else {
      const data = await res.json();
      showAlert('danger', data.detail || 'Gagal menyimpan.');
    }
  } catch {
    showAlert('danger', 'Gagal terhubung ke server.');
  }
}

function showAlert(type, msg) {
  const el = document.getElementById('profileAlert');
  el.className   = `alert alert-${type} small py-2`;
  el.textContent = msg;
}

document.addEventListener('DOMContentLoaded', loadProfile);
</script>
@endsection