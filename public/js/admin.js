// =====================
// DASHBOARD
// =====================
async function loadDashboard() {
  try {
    const [mRes, sRes, mpRes, aRes] = await Promise.all([
      fetch('/api/materi?limit=1'),
      fetch('/api/classroom?limit=1'),
      fetch('/api/mapel'),
      fetch('/api/admin/users'),
    ]);
    const mData  = await mRes.json();
    const sData  = await sRes.json();
    const mpData = await mpRes.json();
    const aData  = await aRes.json();

    setText('statMateri',  mData.total          ?? 0);
    setText('statSekolah', sData.total          ?? 0);
    setText('statMapel',   mpData.items?.length ?? 0);
    setText('statAdmin',   aData.total          ?? 0);

    const res  = await fetch('/api/materi?limit=5&sort=terbaru');
    const data = await res.json();
    renderTabelMateri('tabelMateri', data.items || [], 1, 5);
  } catch {
    setText('statMateri', '-');
  }
}

// =====================
// MATERI ADMIN
// =====================
let materiPage = 1;

async function loadMateriAdmin() {
  materiPage = 1;
  await fetchMateriAdmin();
}

async function fetchMateriAdmin() {
  const q       = document.getElementById('searchMateri')?.value || '';
  const jenjang = document.getElementById('filterJenjang')?.value || '';
  const tipe    = document.getElementById('filterTipe')?.value || '';

  const params = new URLSearchParams({
    limit: 10, page: materiPage, sort: 'terbaru',
    ...(q       && { q }),
    ...(jenjang && { jenjang }),
    ...(tipe    && { tipe }),
  });

  const tbody = document.getElementById('tabelMateriAdmin');
  if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>`;

  try {
    const res  = await fetch(`/api/materi?${params}`);
    const data = await res.json();
    renderTabelMateri('tabelMateriAdmin', data.items || [], materiPage, 10, true);
    renderPaginasiMateri(data.total || 0, 10);
  } catch {
    if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">Gagal memuat data.</td></tr>`;
  }
}

function renderTabelMateri(tbodyId, items, page, perPage, withActions = false) {
  const tbody = document.getElementById(tbodyId);
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `<tr><td colspan="${withActions ? 7 : 6}" class="text-center text-muted py-4">Belum ada materi.</td></tr>`;
    return;
  }

  const EMOJI = { video: '🎬', ppt: '📑', pdf: '📄' };

  tbody.innerHTML = items.map((m, i) => `
    <tr>
      ${withActions ? `<td class="text-muted small">${(page - 1) * perPage + i + 1}</td>` : ''}
      <td>
        <div style="max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:600">
          ${EMOJI[m.tipe] || '📚'} ${m.judul}
        </div>
      </td>
      <td><span class="badge rounded-pill badge-${m.jenjang?.toLowerCase()}">${m.jenjang?.toUpperCase()}</span></td>
      <td><span class="badge rounded-pill badge-${m.tipe}">${m.tipe?.toUpperCase()}</span></td>
      <td class="text-muted small">${m.mata_pelajaran || '-'}</td>
      <td class="text-muted small">${m.created_at?.slice(0, 10) || '-'}</td>
      ${withActions ? `
        <td>
          <div class="d-flex gap-1">
            <a href="/admin/materi/edit/${m.id}" class="btn btn-admin-edit btn-sm">
              <i class="bi bi-pencil"></i>
            </a>
            <button class="btn btn-admin-danger btn-sm" onclick="hapusMateri(${m.id}, '${m.judul.replace(/'/g, "\\'")}')">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </td>` : `
        <td>
          <a href="/admin/materi/edit/${m.id}" class="btn btn-admin-edit btn-sm">
            <i class="bi bi-pencil"></i>
          </a>
        </td>`}
    </tr>`).join('');
}

function renderPaginasiMateri(total, perPage) {
  const wrap = document.getElementById('paginasiMateri');
  if (!wrap) return;
  const totalPages = Math.ceil(total / perPage);
  if (totalPages <= 1) { wrap.innerHTML = ''; return; }

  wrap.innerHTML = `
    <nav>
      <ul class="pagination admin-pagination mb-0">
        <li class="page-item ${materiPage === 1 ? 'disabled' : ''}">
          <button class="page-link" onclick="goPageMateri(${materiPage - 1})">
            <i class="bi bi-chevron-left"></i>
          </button>
        </li>
        ${Array.from({length: totalPages}, (_, i) => i + 1).map(p => `
          <li class="page-item ${p === materiPage ? 'active' : ''}">
            <button class="page-link" onclick="goPageMateri(${p})">${p}</button>
          </li>`).join('')}
        <li class="page-item ${materiPage === totalPages ? 'disabled' : ''}">
          <button class="page-link" onclick="goPageMateri(${materiPage + 1})">
            <i class="bi bi-chevron-right"></i>
          </button>
        </li>
      </ul>
    </nav>`;
}

async function goPageMateri(page) {
  materiPage = page;
  await fetchMateriAdmin();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function hapusMateri(id, judul) {
  if (!confirm(`Hapus materi "${judul}"?`)) return;
  try {
    const res = await fetch(`/api/admin/materi/${id}`, { method: 'DELETE' });
    if (res.ok) { loadMateriAdmin(); }
    else { alert('Gagal menghapus materi.'); }
  } catch { alert('Gagal terhubung ke server.'); }
}

function resetFilterMateri() {
  document.getElementById('searchMateri').value  = '';
  document.getElementById('filterJenjang').value = '';
  document.getElementById('filterTipe').value    = '';
  loadMateriAdmin();
}

// =====================
// FORM MATERI
// =====================
async function loadMapelOptions(selectedId = null, selectedNama = null) {
  const jenjang     = document.getElementById('fJenjang')?.value;
  const datalist    = document.getElementById('mapelList');
  const hiddenInput = document.getElementById('fMapel');
  const mapelInput  = document.getElementById('fMapelInput');
  const mapelHint   = document.getElementById('mapelHint');
  if (!datalist) return;

  if (!jenjang) {
    datalist.innerHTML  = '';
    if (mapelInput) mapelInput.disabled = true;
    if (mapelHint)  mapelHint.textContent = 'Pilih jenjang terlebih dahulu.';
    return;
  }

  try {
    const res  = await fetch(`/api/mapel?jenjang=${jenjang}`);
    const data = await res.json();
    window._mapelItems = data.items || [];

    datalist.innerHTML = window._mapelItems
      .map(m => `<option value="${m.nama}" data-id="${m.id}">`)
      .join('');

    if (mapelInput) {
      mapelInput.disabled    = false;
      mapelInput.placeholder = 'Ketik mata pelajaran...';
    }
    if (mapelHint) mapelHint.textContent = `${window._mapelItems.length} mata pelajaran tersedia.`;

    // Reset input jika jenjang berubah (bukan saat edit)
    if (!selectedId && !selectedNama) {
      mapelInput.value  = '';
      hiddenInput.value = '';
    }

    if (selectedNama) {
      const match = window._mapelItems.find(m => m.nama === selectedNama);
      if (match) hiddenInput.value = match.id;
    } else if (selectedId) {
      const match = window._mapelItems.find(m => String(m.id) === String(selectedId));
      if (match) {
        mapelInput.value  = match.nama;
        hiddenInput.value = match.id;
      }
    }
  } catch {
    datalist.innerHTML = '';
    if (mapelHint) mapelHint.textContent = 'Gagal memuat mata pelajaran.';
  }
}

async function submitMateri(id) {
  const judul     = document.getElementById('fJudul')?.value?.trim();
  const jenjang   = document.getElementById('fJenjang')?.value;
  const tipe      = document.getElementById('fTipe')?.value;
  const mapel_id  = document.getElementById('fMapel')?.value;
  const url       = document.getElementById('fUrl')?.value?.trim();
  const deskripsi = document.getElementById('fDeskripsi')?.value?.trim();

  if (!judul || !jenjang || !tipe || !url) {
    showFormAlert('danger', 'Semua field wajib diisi kecuali deskripsi.');
    return;
  }
  if (!mapel_id) {
    showFormAlert('danger', 'Mata pelajaran tidak ditemukan, pastikan sesuai dengan daftar.');
    return;
  }

  const payload  = { judul, jenjang, tipe, mapel_id: parseInt(mapel_id), url, deskripsi };
  const method   = id ? 'PUT' : 'POST';
  const endpoint = id ? `/api/admin/materi/${id}` : '/api/admin/materi';

  try {
    const res = await fetch(endpoint, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    if (res.ok) {
      window.location.href = '/admin/materi?success=1';
    } else {
      const data = await res.json();
      showFormAlert('danger', data.detail || 'Gagal menyimpan materi.');
    }
  } catch {
    showFormAlert('danger', 'Gagal terhubung ke server.');
  }
}

function showFormAlert(type, msg) {
  const el = document.getElementById('formAlert');
  if (!el) return;
  el.className   = `alert alert-${type}`;
  el.textContent = msg;
}

// =====================
// HELPER
// =====================
function setText(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}

// =====================
// DOM READY
// =====================
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('fMapelInput')?.addEventListener('input', () => {
    const val   = document.getElementById('fMapelInput').value;
    const match = (window._mapelItems || []).find(m => m.nama === val);
    document.getElementById('fMapel').value = match ? match.id : '';
  });
});
