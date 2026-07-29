let adminModal = null;
let sekolahOptionsLoaded = false;

document.addEventListener('DOMContentLoaded', () => {
  adminModal = new bootstrap.Modal(document.getElementById('modalAdmin'));
  loadAdminUsers();
});

async function loadAdminUsers() {
  const tbody = document.getElementById('tabelAdmin');
  if (tbody) tbody.innerHTML = `
    <tr>
      <td colspan="6" class="text-center py-4">
        <div class="spinner-border spinner-border-sm text-success"></div>
      </td>
    </tr>`;

  try {
    const res  = await fetch('/api/admin/users');
    const data = await res.json();
    if (!res.ok) throw new Error(data.detail || 'Gagal memuat data.');
    renderTabelAdmin(data.items || []);
  } catch (err) {
    if (tbody) tbody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center text-muted py-4">${err.message || 'Gagal memuat data.'}</td>
      </tr>`;
  }
}

const ROLE_BADGE = {
  admin:   { label: 'Admin',   class: 'bg-primary' },
  guru:    { label: 'Guru',    class: 'bg-warning text-dark' },
  sekolah: { label: 'Sekolah', class: 'bg-info text-dark' },
};

function renderTabelAdmin(items) {
  const tbody = document.getElementById('tabelAdmin');
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center text-muted py-4">Belum ada administrator.</td>
      </tr>`;
    return;
  }

  tbody.innerHTML = items.map((a, i) => {
    const badge = ROLE_BADGE[a.role] || ROLE_BADGE.admin;
    return `
    <tr>
      <td class="text-muted small">${i + 1}</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="admin-avatar-sm">
            <i class="bi bi-person-circle"></i>
          </div>
          <div>
            <div style="font-weight:600">${a.nama || '-'}</div>
            ${a.role === 'sekolah' && a.sekolah ? `<div class="small text-muted">${a.sekolah.nama}</div>` : ''}
          </div>
        </div>
      </td>
      <td>
        <code class="text-success" style="font-size:0.85rem">@${a.username}</code>
      </td>
      <td>
        <span class="badge rounded-pill ${badge.class}">${badge.label}</span>
      </td>
      <td class="text-muted small">${a.created_at?.slice(0, 10) || '-'}</td>
      <td>
        ${a.id === CURRENT_ADMIN_ID
          ? `<span class="text-muted small"><i class="bi bi-lock me-1"></i>Akun aktif</span>`
          : `<button class="btn btn-admin-danger btn-sm"
              onclick="hapusAdmin(${a.id}, '${a.username.replace(/'/g, "\\'")}')">
              <i class="bi bi-trash me-1"></i>Hapus
            </button>`
        }
      </td>
    </tr>`;
  }).join('');
}

function showFormTambahAdmin() {
  document.getElementById('fNamaAdmin').value     = '';
  document.getElementById('fUsernameAdmin').value = '';
  document.getElementById('fPasswordAdmin').value = '';
  document.getElementById('fRoleAdmin').value     = 'admin';
  document.getElementById('wrapSekolahPicker').classList.add('d-none');
  document.getElementById('fSekolahAdmin').value  = '';
  document.getElementById('adminAlert').classList.add('d-none');
  adminModal.show();
}

function toggleSekolahPicker() {
  const role = document.getElementById('fRoleAdmin').value;
  const wrap = document.getElementById('wrapSekolahPicker');

  if (role === 'sekolah') {
    wrap.classList.remove('d-none');
    if (!sekolahOptionsLoaded) loadSekolahPickerOptions();
  } else {
    wrap.classList.add('d-none');
  }
}

async function loadSekolahPickerOptions() {
  const select = document.getElementById('fSekolahAdmin');
  select.innerHTML = `<option value="">Memuat daftar sekolah...</option>`;

  try {
    const res = await fetch('/api/classroom');
    const data = await res.json();
    const items = data.items || [];

    select.innerHTML = items.length
      ? `<option value="">-- Pilih sekolah --</option>` +
        items.map(s => `<option value="${s.id}">${s.nama}</option>`).join('')
      : `<option value="">Belum ada data sekolah</option>`;

    sekolahOptionsLoaded = true;
  } catch {
    select.innerHTML = `<option value="">Gagal memuat daftar sekolah</option>`;
  }
}

async function submitAdmin() {
  const nama      = document.getElementById('fNamaAdmin').value.trim();
  const username  = document.getElementById('fUsernameAdmin').value.trim();
  const password  = document.getElementById('fPasswordAdmin').value;
  const role      = document.getElementById('fRoleAdmin').value;
  const sekolahId = document.getElementById('fSekolahAdmin').value;

  if (!username || !password) {
    showAdminAlert('danger', 'Username dan password wajib diisi.');
    return;
  }
  if (password.length < 6) {
    showAdminAlert('danger', 'Password minimal 6 karakter.');
    return;
  }
  if (role === 'sekolah' && !sekolahId) {
    showAdminAlert('danger', 'Pilih sekolah untuk akun dengan peran Sekolah.');
    return;
  }

  try {
    const res = await fetch('/api/admin/users', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username, password, nama, role,
        sekolah_id: role === 'sekolah' ? sekolahId : null,
      }),
    });

    if (res.ok) {
      adminModal.hide();
      loadAdminUsers();
    } else {
      const data = await res.json();
      showAdminAlert('danger', data.detail || 'Gagal menyimpan.');
    }
  } catch {
    showAdminAlert('danger', 'Gagal terhubung ke server.');
  }
}

async function hapusAdmin(id, username) {
  if (!confirm(`Hapus admin "@${username}"? Tindakan ini tidak bisa dibatalkan.`)) return;

  try {
    const res = await fetch(`/api/admin/users/${id}`, { method: 'DELETE' });
    if (res.ok) {
      loadAdminUsers();
    } else {
      const data = await res.json();
      alert(data.detail || 'Gagal menghapus.');
    }
  } catch {
    alert('Gagal terhubung ke server.');
  }
}

function togglePassAdmin() {
  const input = document.getElementById('fPasswordAdmin');
  const icon  = document.getElementById('eyeAdminIcon');
  input.type     = input.type === 'password' ? 'text' : 'password';
  icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function showAdminAlert(type, msg) {
  const el = document.getElementById('adminAlert');
  if (!el) return;
  el.className   = `alert alert-${type} small py-2`;
  el.textContent = msg;
  el.classList.remove('d-none');
}