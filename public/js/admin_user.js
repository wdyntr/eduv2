let userModal = null;
let sekolahOptionsLoaded = false;
let roleOptions = [];

document.addEventListener('DOMContentLoaded', () => {
  userModal = new bootstrap.Modal(document.getElementById('modalUser'));
  loadUsers();
  loadRoleOptions();
});


async function loadRoleOptions() {
  const select = document.getElementById('fRoleUser');
  if (!select) return;

  select.innerHTML = '<option value="">Memuat role...</option>';
  try {
    const res = await fetch('/api/roles/options');
    const data = await res.json();
    if (!res.ok) throw new Error(data.detail || 'Gagal memuat role.');

    roleOptions = (data.items || []).map(role => ({
      name: role.name,
      label: role.label || formatRoleLabel(role.name),
      requires_sekolah: Boolean(role.requires_sekolah),
    }));

    select.innerHTML = roleOptions.length
      ? roleOptions.map(r => `<option value="${escapeHtml(r.name)}">${escapeHtml(r.label)}</option>`).join('')
      : '<option value="">Belum ada role</option>';

    toggleSekolahPicker();
  } catch (err) {
    select.innerHTML = '<option value="">Gagal memuat role</option>';
    roleOptions = [];
  }
}

function formatRoleLabel(name) {
  return String(name || '')
    .replace(/[_-]+/g, ' ')
    .replace(/\b\w/g, c => c.toUpperCase());
}

function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  }[c]));
}

function getSelectedRole() {
  const name = document.getElementById('fRoleUser')?.value || '';
  return roleOptions.find(r => r.name === name) || null;
}

async function loadUsers() {
  const tbody = document.getElementById('tabelUser');
  if (tbody) tbody.innerHTML = `
    <tr>
      <td colspan="6" class="text-center py-4">
        <div class="spinner-border spinner-border-sm text-success"></div>
      </td>
    </tr>`;

  try {
    const res  = await fetch('/api/users');
    const data = await res.json();
    if (!res.ok) throw new Error(data.detail || 'Gagal memuat data.');
    renderTabelUser(data.items || []);
  } catch (err) {
    if (tbody) tbody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center text-muted py-4">${err.message || 'Gagal memuat data.'}</td>
      </tr>`;
  }
}

const ROLE_BADGE = {
  admin_sistem:    { label: 'Admin Sistem',     class: 'bg-primary' },
  operator_konten: { label: 'Operator Konten',  class: 'bg-success' },
  sekolah:         { label: 'Sekolah',          class: 'bg-info text-dark' },
  penulis:         { label: 'Penulis',          class: 'bg-warning text-dark' },
  pereview:        { label: 'Pereview',         class: 'bg-secondary' },
};

function renderTabelUser(items) {
  const tbody = document.getElementById('tabelUser');
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center text-muted py-4">Belum ada user.</td>
      </tr>`;
    return;
  }

  tbody.innerHTML = items.map((u, i) => {
    const badge = ROLE_BADGE[u.role] || { label: formatRoleLabel(u.role || 'tanpa role'), class: 'bg-dark' };
    return `
    <tr>
      <td class="text-muted small">${i + 1}</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="admin-avatar-sm">
            <i class="bi bi-person-circle"></i>
          </div>
          <div>
            <div style="font-weight:600">${escapeHtml(u.nama || '-')}</div>
            ${u.role === 'sekolah' && u.sekolah ? `<div class="small text-muted">${escapeHtml(u.sekolah.nama)}</div>` : ''}
          </div>
        </div>
      </td>
      <td>
        <code class="text-success" style="font-size:0.85rem">@${escapeHtml(u.username)}</code>
      </td>
      <td>
        <span class="badge rounded-pill ${badge.class}">${badge.label}</span>
      </td>
      <td class="text-muted small">${u.created_at?.slice(0, 10) || '-'}</td>
      <td>
        ${u.id === CURRENT_USER_ID
          ? `<span class="text-muted small"><i class="bi bi-lock me-1"></i>Akun aktif</span>`
          : `<button class="btn btn-admin-danger btn-sm"
              onclick="hapusUser(${u.id}, '${u.username.replace(/'/g, "\\'")}')">
              <i class="bi bi-trash me-1"></i>Hapus
            </button>`
        }
      </td>
    </tr>`;
  }).join('');
}

function showFormTambahUser() {
  document.getElementById('fNamaUser').value     = '';
  document.getElementById('fUsernameUser').value = '';
  document.getElementById('fPasswordUser').value = '';
  const firstRole = roleOptions[0]?.name || '';
  document.getElementById('fRoleUser').value     = firstRole;
  document.getElementById('fSekolahUser').value  = '';
  document.getElementById('userAlert').classList.add('d-none');
  toggleSekolahPicker();
  userModal.show();
}

function toggleSekolahPicker() {
  const role = getSelectedRole();
  const wrap = document.getElementById('wrapSekolahPicker');

  if (role?.requires_sekolah) {
    wrap.classList.remove('d-none');
    if (!sekolahOptionsLoaded) loadSekolahPickerOptions();
  } else {
    wrap.classList.add('d-none');
  }
}

async function loadSekolahPickerOptions() {
  const select = document.getElementById('fSekolahUser');
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

async function submitUser() {
  const nama      = document.getElementById('fNamaUser').value.trim();
  const username  = document.getElementById('fUsernameUser').value.trim();
  const password  = document.getElementById('fPasswordUser').value;
  const role      = document.getElementById('fRoleUser').value;
  const sekolahId = document.getElementById('fSekolahUser').value;
  const selectedRole = getSelectedRole();

  if (!username || !password) {
    showUserAlert('danger', 'Username dan password wajib diisi.');
    return;
  }
  if (password.length < 6) {
    showUserAlert('danger', 'Password minimal 6 karakter.');
    return;
  }
  if (selectedRole?.requires_sekolah && !sekolahId) {
    showUserAlert('danger', `Pilih sekolah untuk akun dengan peran ${selectedRole.label}.`);
    return;
  }

  try {
    const res = await fetch('/api/users', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username, password, nama, role,
        sekolah_id: selectedRole?.requires_sekolah ? sekolahId : null,
      }),
    });

    if (res.ok) {
      userModal.hide();
      loadUsers();
  loadRoleOptions();
    } else {
      const data = await res.json();
      showUserAlert('danger', data.detail || 'Gagal menyimpan.');
    }
  } catch {
    showUserAlert('danger', 'Gagal terhubung ke server.');
  }
}

async function hapusUser(id, username) {
  if (!confirm(`Hapus user "@${username}"? Tindakan ini tidak bisa dibatalkan.`)) return;

  try {
    const res = await fetch(`/api/users/${id}`, { method: 'DELETE' });
    if (res.ok) {
      loadUsers();
  loadRoleOptions();
    } else {
      const data = await res.json();
      alert(data.detail || 'Gagal menghapus.');
    }
  } catch {
    alert('Gagal terhubung ke server.');
  }
}

function togglePassUser() {
  const input = document.getElementById('fPasswordUser');
  const icon  = document.getElementById('eyeUserIcon');
  input.type     = input.type === 'password' ? 'text' : 'password';
  icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function showUserAlert(type, msg) {
  const el = document.getElementById('userAlert');
  if (!el) return;
  el.className   = `alert alert-${type} small py-2`;
  el.textContent = msg;
  el.classList.remove('d-none');
}