let sekolahPage = 1;
let sekolahModal = null;

document.addEventListener('DOMContentLoaded', () => {
  sekolahModal = new bootstrap.Modal(document.getElementById('modalSekolah'));
});

async function loadSekolahAdmin() {
  sekolahPage = 1;
  await fetchSekolahAdmin();
}

async function fetchSekolahAdmin() {
  const search  = document.getElementById('searchSekolah')?.value || '';
  const jenjang = document.getElementById('filterJenjangAdmin')?.value || '';

  const params = new URLSearchParams({
    limit: 10, page: sekolahPage,
    ...(search  && { search }),
    ...(jenjang && { jenjang }),
  });

  const tbody = document.getElementById('tabelSekolah');
  if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>`;

  try {
    const res  = await fetch(`/api/classroom?${params}`);
    const data = await res.json();
    renderTabelSekolah(data.items || []);
    renderPaginasiSekolah(data.total || 0);
  } catch {
    if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Gagal memuat data.</td></tr>`;
  }
}

function renderTabelSekolah(items) {
  const tbody = document.getElementById('tabelSekolah');
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Belum ada sekolah.</td></tr>`;
    return;
  }

  tbody.innerHTML = items.map((s, i) => `
    <tr>
      <td class="text-muted small">${(sekolahPage - 1) * 10 + i + 1}</td>
      <td style="font-weight:600">${s.nama}</td>
      <td><span class="badge rounded-pill badge-${s.jenjang?.toLowerCase()}">${s.jenjang?.toUpperCase()}</span></td>
      <td class="text-muted small">${s.kota_kabupaten || '-'}</td>
      <td>
        ${s.classroom_url
          ? `<a href="${s.classroom_url}" target="_blank" class="text-success small">
              <i class="bi bi-box-arrow-up-right me-1"></i>Buka
             </a>`
          : `<span class="text-muted small">-</span>`}
      </td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn btn-admin-edit btn-sm" onclick="showFormEdit(${s.id}, '${s.nama.replace(/'/g,"\\'")}', '${s.jenjang}', '${s.kota_kabupaten || ''}', '${s.classroom_url || ''}')">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-admin-danger btn-sm" onclick="hapusSekolah(${s.id}, '${s.nama.replace(/'/g,"\\'")}')">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </td>
    </tr>`).join('');
}

function renderPaginasiSekolah(total) {
  const wrap       = document.getElementById('paginasiSekolah');
  if (!wrap) return;
  const totalPages = Math.ceil(total / 10);
  if (totalPages <= 1) { wrap.innerHTML = ''; return; }

  wrap.innerHTML = `
    <nav>
      <ul class="pagination admin-pagination mb-0">
        <li class="page-item ${sekolahPage === 1 ? 'disabled' : ''}">
          <button class="page-link" onclick="goPageSekolah(${sekolahPage - 1})">
            <i class="bi bi-chevron-left"></i>
          </button>
        </li>
        ${Array.from({length: totalPages}, (_, i) => i + 1).map(p => `
          <li class="page-item ${p === sekolahPage ? 'active' : ''}">
            <button class="page-link" onclick="goPageSekolah(${p})">${p}</button>
          </li>`).join('')}
        <li class="page-item ${sekolahPage === totalPages ? 'disabled' : ''}">
          <button class="page-link" onclick="goPageSekolah(${sekolahPage + 1})">
            <i class="bi bi-chevron-right"></i>
          </button>
        </li>
      </ul>
    </nav>`;
}

async function goPageSekolah(page) {
  sekolahPage = page;
  await fetchSekolahAdmin();
}

function showFormTambah() {
  document.getElementById('modalSekolahTitle').textContent = 'Tambah Sekolah';
  document.getElementById('fSekolahId').value      = '';
  document.getElementById('fNamaSekolah').value    = '';
  document.getElementById('fJenjangSekolah').value = '';
  document.getElementById('fKotaSekolah').value    = '';
  document.getElementById('fUrlSekolah').value     = '';
  document.getElementById('sekolahAlert').classList.add('d-none');
  sekolahModal.show();
}

function showFormEdit(id, nama, jenjang, kota, url) {
  document.getElementById('modalSekolahTitle').textContent = 'Edit Sekolah';
  document.getElementById('fSekolahId').value      = id;
  document.getElementById('fNamaSekolah').value    = nama;
  document.getElementById('fJenjangSekolah').value = jenjang;
  document.getElementById('fKotaSekolah').value    = kota;
  document.getElementById('fUrlSekolah').value     = url;
  document.getElementById('sekolahAlert').classList.add('d-none');
  sekolahModal.show();
}

async function submitSekolah() {
  const id     = document.getElementById('fSekolahId').value;
  const nama   = document.getElementById('fNamaSekolah').value.trim();
  const jenjang= document.getElementById('fJenjangSekolah').value;
  const kota   = document.getElementById('fKotaSekolah').value.trim();
  const url    = document.getElementById('fUrlSekolah').value.trim();

  if (!nama || !jenjang) {
    showSekolahAlert('danger', 'Nama sekolah dan jenjang wajib diisi.');
    return;
  }

  const payload  = { nama, jenjang, kota_kabupaten: kota, classroom_url: url };
  const method   = id ? 'PUT' : 'POST';
  const endpoint = id ? `/api/admin/classroom/${id}` : '/api/admin/classroom';

  try {
    const res = await fetch(endpoint, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    if (res.ok) {
      sekolahModal.hide();
      loadSekolahAdmin();
    } else {
      const data = await res.json();
      showSekolahAlert('danger', data.detail || 'Gagal menyimpan.');
    }
  } catch {
    showSekolahAlert('danger', 'Gagal terhubung ke server.');
  }
}

async function hapusSekolah(id, nama) {
  if (!confirm(`Hapus sekolah "${nama}"?`)) return;
  try {
    const res = await fetch(`/api/admin/classroom/${id}`, { method: 'DELETE' });
    if (res.ok) { loadSekolahAdmin(); }
    else { alert('Gagal menghapus sekolah.'); }
  } catch { alert('Gagal terhubung ke server.'); }
}

function showSekolahAlert(type, msg) {
  const el = document.getElementById('sekolahAlert');
  if (!el) return;
  el.className   = `alert alert-${type}`;
  el.textContent = msg;
}

function resetFilterSekolah() {
  document.getElementById('searchSekolah').value       = '';
  document.getElementById('filterJenjangAdmin').value  = '';
  loadSekolahAdmin();
}
