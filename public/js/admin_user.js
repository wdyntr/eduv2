let adminModal = null;

document.addEventListener('DOMContentLoaded', () => {
  adminModal = new bootstrap.Modal(document.getElementById('modalAdmin'));
  loadAdminUsers(); // tambahkan ini
});

async function loadAdminUsers() {
  const tbody = document.getElementById('tabelAdmin');
  if (tbody) tbody.innerHTML = `
    <tr>
      <td colspan="5" class="text-center py-4">
        <div class="spinner-border spinner-border-sm text-success"></div>
      </td>
    </tr>`;

  try {
    const res  = await fetch('/api/admin/users');
    const data = await res.json();
    renderTabelAdmin(data.items || []);
  } catch {
    if (tbody) tbody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center text-muted py-4">Gagal memuat data.</td>
      </tr>`;
  }
}

function renderTabelAdmin(items) {
  const tbody = document.getElementById('tabelAdmin');
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center text-muted py-4">Belum ada administrator.</td>
      </tr>`;
    return;
  }

  tbody.innerHTML = items.map((a, i) => `
    <tr>
      <td class="text-muted small">${i + 1}</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="admin-avatar-sm">
            <i class="bi bi-person-circle"></i>
          </div>
          <span style="font-weight:600">${a.nama || '-'}</span>
        </div>
      </td>
      <td>
        <code class="text-success" style="font-size:0.85rem">@${a.username}</code>
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
    </tr>`).join('');
}

function showFormTambahAdmin() {
  document.getElementById('fNamaAdmin').value     = '';
  document.getElementById('fUsernameAdmin').value = '';
  document.getElementById('fPasswordAdmin').value = '';
  document.getElementById('adminAlert').classList.add('d-none');
  adminModal.show();
}

async function submitAdmin() {
  const nama     = document.getElementById('fNamaAdmin').value.trim();
  const username = document.getElementById('fUsernameAdmin').value.trim();
  const password = document.getElementById('fPasswordAdmin').value;

  if (!username || !password) {
    showAdminAlert('danger', 'Username dan password wajib diisi.');
    return;
  }
  if (password.length < 6) {
    showAdminAlert('danger', 'Password minimal 6 karakter.');
    return;
  }

  try {
    const res = await fetch('/api/admin/users', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password, nama }),
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
}
