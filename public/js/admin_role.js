let roleModal = null;
let roles = [];
let permissions = [];

document.addEventListener('DOMContentLoaded', async () => {
  const modal = document.getElementById('modalRole');
  if (modal) roleModal = new bootstrap.Modal(modal);
  await Promise.all([loadPermissions(), loadRoles()]);
});

async function loadRoles() {
  const tbody = document.getElementById('tabelRole');

  try {
    const res = await fetch('/api/roles');
    const data = await res.json();

    if (!res.ok) {
      throw new Error(
        data.detail ||
        data.message ||
        'Gagal memuat role.'
      );
    }

    roles = data.items || [];
    renderRoles();

  } catch (err) {
    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5"
              class="text-center text-danger py-4">
            ${escapeRoleHtml(err.message)}
          </td>
        </tr>`;
    }
  }
}

async function loadPermissions() {
  const box = document.getElementById('permissionList');

  try {
    const res = await fetch('/api/permissions');
    const data = await res.json();

    if (!res.ok) {
      throw new Error(
        data.detail ||
        data.message ||
        'Gagal memuat permission.'
      );
    }

    permissions = data.items || [];
    renderPermissionList();

  } catch (err) {
    if (box) {
      box.innerHTML = `
        <div class="text-danger small">
          ${escapeRoleHtml(err.message)}
        </div>`;
    }
  }
}

function renderRoles() {
  const tbody = document.getElementById('tabelRole');
  if (!tbody) return;

  if (!roles.length) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada role.</td></tr>';
    return;
  }

  tbody.innerHTML = roles.map((role, index) => {
    const permissionHtml = role.permissions?.length
      ? role.permissions.map(p => `<span class="badge bg-light text-dark border me-1 mb-1">${escapeRoleHtml(formatRoleLabel(p))}</span>`).join('')
      : '<span class="text-muted small">Tidak ada permission</span>';

    return `<tr>
      <td class="text-muted small">${index + 1}</td>
      <td>
        <div class="fw-semibold">${escapeRoleHtml(role.label || formatRoleLabel(role.name))}</div>
        <code class="small">${escapeRoleHtml(role.name)}</code>
      </td>
      <td>${permissionHtml}</td>
      <td>${role.jumlah_user ?? 0}</td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn btn-admin-edit btn-sm" onclick="editRole(${role.id})" title="Edit">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-admin-danger btn-sm" onclick="deleteRole(${role.id})" title="Hapus"
            ${Number(role.jumlah_user) > 0 ? 'disabled' : ''}>
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function renderPermissionList(selected = []) {
  const box = document.getElementById('permissionList');
  if (!box) return;

  if (!permissions.length) {
    box.innerHTML = '<div class="text-muted small">Belum ada permission.</div>';
    return;
  }

  const selectedSet = new Set(selected);
  box.innerHTML = permissions.map((permission, index) => `
    <div class="col-md-6">
      <label class="border rounded-3 p-2 d-flex align-items-center gap-2 h-100">
        <input class="form-check-input mt-0 role-permission" type="checkbox"
               value="${escapeRoleHtml(permission)}" ${selectedSet.has(permission) ? 'checked' : ''}>
        <span class="small">${escapeRoleHtml(formatRoleLabel(permission))}</span>
      </label>
    </div>`).join('');
}

function showRoleForm(role = null) {
  document.getElementById('modalRoleTitle').textContent = role ? 'Edit Role' : 'Tambah Role';
  document.getElementById('fRoleId').value = role?.id || '';
  document.getElementById('fRoleName').value = role?.name || '';
  document.getElementById('fRoleRequiresSekolah').checked = Boolean(role?.requires_sekolah);
  document.getElementById('roleAlert').classList.add('d-none');
  renderPermissionList(role?.permissions || []);
  roleModal?.show();
}

function editRole(id) {
  const role = roles.find(r => Number(r.id) === Number(id));
  if (role) showRoleForm(role);
}

async function submitRole() {
  const id = document.getElementById('fRoleId').value;
  const name = document.getElementById('fRoleName').value.trim();
  const requiresSekolah = document.getElementById('fRoleRequiresSekolah').checked;
  const selectedPermissions = [...document.querySelectorAll('.role-permission:checked')].map(el => el.value);

  if (!/^[A-Za-z0-9_-]+$/.test(name)) {
    showRoleAlert('danger', 'Nama role hanya boleh berisi huruf, angka, underscore, atau tanda minus.');
    return;
  }

  const method = id ? 'PUT' : 'POST';
  const endpoint = id
    ? `/api/roles/${id}`
    : '/api/roles';

  try {
    const res = await fetch(endpoint, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, permissions: selectedPermissions, requires_sekolah: requiresSekolah }),
    });
    const data = await res.json();

    if (!res.ok) {
      showRoleAlert('danger', data.detail || data.message || 'Gagal menyimpan role.');
      return;
    }

    roleModal?.hide();
    await loadRoles();
  } catch {
    showRoleAlert('danger', 'Gagal terhubung ke server.');
  }
}

async function deleteRole(id) {
  const role = roles.find(r => Number(r.id) === Number(id));
  if (!role) return;
  if (Number(role.jumlah_user) > 0) {
    alert(`Tidak bisa dihapus, masih ada ${role.jumlah_user} user memakai role ini.`);
    return;
  }

  if (!confirm(`Hapus role "${role.label || role.name}"? Tindakan ini tidak bisa dibatalkan.`)) return;

  try {
    const res = await fetch(`/api/roles/${id}`, {
      method: 'DELETE'
    });
    const data = await res.json();
    if (!res.ok) {
      alert(data.detail || 'Gagal menghapus role.');
      return;
    }
    await loadRoles();
  } catch {
    alert('Gagal terhubung ke server.');
  }
}

function showRoleAlert(type, message) {
  const el = document.getElementById('roleAlert');
  if (!el) return;
  el.className = `alert alert-${type}`;
  el.textContent = message;
}

function formatRoleLabel(value) {
  return String(value || '')
    .replace(/[_-]+/g, ' ')
    .replace(/\b\w/g, c => c.toUpperCase());
}

function escapeRoleHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  }[c]));
}