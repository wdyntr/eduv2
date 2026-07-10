let mapelPage = 1;
let mapelModal = null;
let allMapel   = [];
let filteredMapel = [];
const MAPEL_PER_PAGE = 10;

document.addEventListener('DOMContentLoaded', () => {
  mapelModal = new bootstrap.Modal(document.getElementById('modalMapel'));
});

async function loadMapelAdmin() {
  mapelPage = 1;
  await fetchMapelAdmin();
}

async function fetchMapelAdmin() {
  const search  = document.getElementById('searchMapel')?.value.toLowerCase() || '';
  const jenjang = document.getElementById('filterJenjangMapel')?.value || '';

  const tbody = document.getElementById('tabelMapel');
  if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>`;

  try {
    const params = new URLSearchParams({ ...(jenjang && { jenjang }) });
    const res    = await fetch(`/api/mapel?${params}`);
    const data   = await res.json();
    allMapel     = data.items || [];

    // Filter lokal berdasarkan search
    filteredMapel = search
      ? allMapel.filter(m => m.nama.toLowerCase().includes(search))
      : allMapel;

    await renderMapelPage();
  } catch {
    if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Gagal memuat data.</td></tr>`;
  }
}

async function renderMapelPage() {
  const start = (mapelPage - 1) * MAPEL_PER_PAGE;
  const pageItems = filteredMapel.slice(start, start + MAPEL_PER_PAGE);
  await renderTabelMapel(pageItems, start);
  renderPaginasiMapel(filteredMapel.length);
}

function goPageMapel(page) {
  if (page < 1) return;
  mapelPage = page;
  renderMapelPage();
}

function renderPaginasiMapel(total) {
  const wrap = document.getElementById('paginasiMapel');
  if (!wrap) return;
  const totalPages = Math.ceil(total / MAPEL_PER_PAGE);
  if (totalPages <= 1) { wrap.innerHTML = ''; return; }

  wrap.innerHTML = `
    <nav>
      <ul class="pagination admin-pagination mb-0">
        <li class="page-item ${mapelPage === 1 ? 'disabled' : ''}">
          <button class="page-link" onclick="goPageMapel(${mapelPage - 1})">
            <i class="bi bi-chevron-left"></i>
          </button>
        </li>
        ${Array.from({length: totalPages}, (_, i) => i + 1).map(p => `
          <li class="page-item ${p === mapelPage ? 'active' : ''}">
            <button class="page-link" onclick="goPageMapel(${p})">${p}</button>
          </li>`).join('')}
        <li class="page-item ${mapelPage === totalPages ? 'disabled' : ''}">
          <button class="page-link" onclick="goPageMapel(${mapelPage + 1})">
            <i class="bi bi-chevron-right"></i>
          </button>
        </li>
      </ul>
    </nav>`;
}

async function renderTabelMapel(items, startIndex = 0) {
  const tbody = document.getElementById('tabelMapel');
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Belum ada mata pelajaran.</td></tr>`;
    return;
  }

  // Fetch jumlah materi hanya untuk baris yang tampil di halaman ini
  const counts = {};
  await Promise.all(items.map(async m => {
    try {
      const res  = await fetch(`/api/materi?limit=1&mapel=${encodeURIComponent(m.nama.toLowerCase())}&jenjang=${m.jenjang}`);
      const data = await res.json();
      counts[m.id] = data.total || 0;
    } catch {
      counts[m.id] = 0;
    }
  }));

  tbody.innerHTML = items.map((m, i) => `
    <tr>
      <td class="text-muted small">${startIndex + i + 1}</td>
      <td style="font-weight:600">
        <i class="bi bi-book text-success me-2"></i>${m.nama}
      </td>
      <td><span class="badge rounded-pill badge-${m.jenjang}">${m.jenjang?.toUpperCase()}</span></td>
      <td>
        <span class="badge bg-light text-muted border">
          ${counts[m.id]} materi
        </span>
      </td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn btn-admin-edit btn-sm"
            onclick="showFormEditMapel(${m.id}, '${m.nama.replace(/'/g,"\\'")}', '${m.jenjang}')">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-admin-danger btn-sm"
            onclick="hapusMapel(${m.id}, '${m.nama.replace(/'/g,"\\'")}', ${counts[m.id]})">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </td>
    </tr>`).join('');
}

function showFormTambahMapel() {
  document.getElementById('modalMapelTitle').textContent = 'Tambah Mata Pelajaran';
  document.getElementById('fMapelId').value      = '';
  document.getElementById('fNamaMapel').value    = '';
  document.getElementById('fJenjangMapel').value = '';
  document.getElementById('mapelAlert').classList.add('d-none');
  mapelModal.show();
}

function showFormEditMapel(id, nama, jenjang) {
  document.getElementById('modalMapelTitle').textContent = 'Edit Mata Pelajaran';
  document.getElementById('fMapelId').value      = id;
  document.getElementById('fNamaMapel').value    = nama;
  document.getElementById('fJenjangMapel').value = jenjang;
  document.getElementById('mapelAlert').classList.add('d-none');
  mapelModal.show();
}

async function submitMapel() {
  const id      = document.getElementById('fMapelId').value;
  const nama    = document.getElementById('fNamaMapel').value.trim();
  const jenjang = document.getElementById('fJenjangMapel').value;

  if (!nama || !jenjang) {
    showMapelAlert('danger', 'Nama dan jenjang wajib diisi.');
    return;
  }

  const payload  = { nama, jenjang };
  const method   = id ? 'PUT' : 'POST';
  const endpoint = id ? `/api/admin/mapel/${id}` : '/api/admin/mapel';

  try {
    const res = await fetch(endpoint, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    if (res.ok) {
      mapelModal.hide();
      loadMapelAdmin();
    } else {
      const data = await res.json();
      showMapelAlert('danger', data.detail || 'Gagal menyimpan.');
    }
  } catch {
    showMapelAlert('danger', 'Gagal terhubung ke server.');
  }
}

async function hapusMapel(id, nama, jumlahMateri) {
  if (jumlahMateri > 0) {
    alert(`Tidak bisa menghapus "${nama}" karena masih memiliki ${jumlahMateri} materi. Hapus materinya terlebih dahulu.`);
    return;
  }
  if (!confirm(`Hapus mata pelajaran "${nama}"?`)) return;
  try {
    const res = await fetch(`/api/admin/mapel/${id}`, { method: 'DELETE' });
    if (res.ok) { loadMapelAdmin(); }
    else {
      const data = await res.json();
      alert(data.detail || 'Gagal menghapus.');
    }
  } catch { alert('Gagal terhubung ke server.'); }
}

function showMapelAlert(type, msg) {
  const el = document.getElementById('mapelAlert');
  if (!el) return;
  el.className   = `alert alert-${type}`;
  el.textContent = msg;
}

function resetFilterMapel() {
  document.getElementById('searchMapel').value        = '';
  document.getElementById('filterJenjangMapel').value = '';
  loadMapelAdmin();
}
