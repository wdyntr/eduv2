let sekolahPage = 1;
let sekolahModal = null;

document.addEventListener('DOMContentLoaded', () => {
  const modalEl = document.getElementById('modalSekolah');
  if (modalEl) sekolahModal = new bootstrap.Modal(modalEl);
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
  if (tbody) tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>`;

  try {
    const res  = await fetch(`/api/classroom?${params}`);
    const data = await res.json();
    renderTabelSekolah(data.items || []);
    renderPaginasiSekolah(data.total || 0);
  } catch {
    if (tbody) tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4">Gagal memuat data.</td></tr>`;
  }
}

function renderTabelSekolah(items) {
  const tbody = document.getElementById('tabelSekolah');
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4">Belum ada sekolah.</td></tr>`;
    return;
  }

  tbody.innerHTML = items.map((s, i) => `
    <tr>
      <td class="text-muted small">${(sekolahPage - 1) * 10 + i + 1}</td>
      <td style="font-weight:600">${s.nama}</td>
      <td><span class="badge rounded-pill badge-${s.jenjang?.toLowerCase()}">${s.jenjang?.toUpperCase()}</span></td>
      <td class="text-muted small">${s.kota_kabupaten || '-'}</td>
      <td>
        <span class="badge rounded-pill ${s.kelas_terisi > 0 ? 'bg-success-subtle text-success' : 'bg-light text-muted border'}">
          ${s.kelas_terisi ?? 0} kelas
        </span>
      </td>
      <td class="text-center">${s.total_task ?? '-'}</td>
      <td class="text-center">${s.total_materi ?? '-'}</td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn btn-admin-edit btn-sm" title="Kelola Kelas per Mapel" onclick="location.href='/admin/classroom/${s.id}'">
            <i class="bi bi-collection-play"></i>
          </button>
          <button class="btn btn-admin-edit btn-sm" title="Edit Sekolah" onclick="showFormEdit(${s.id}, '${s.nama.replace(/'/g,"\\'")}', '${s.jenjang}', '${s.kota_kabupaten || ''}')">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-admin-danger btn-sm" title="Hapus" onclick="hapusSekolah(${s.id}, '${s.nama.replace(/'/g,"\\'")}')">
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
  document.getElementById('sekolahAlert').classList.add('d-none');
  sekolahModal.show();
}

function showFormEdit(id, nama, jenjang, kota) {
  document.getElementById('modalSekolahTitle').textContent = 'Edit Sekolah';
  document.getElementById('fSekolahId').value      = id;
  document.getElementById('fNamaSekolah').value    = nama;
  document.getElementById('fJenjangSekolah').value = jenjang;
  document.getElementById('fKotaSekolah').value    = kota;
  document.getElementById('sekolahAlert').classList.add('d-none');
  sekolahModal.show();
}

async function submitSekolah() {
  const id     = document.getElementById('fSekolahId').value;
  const nama   = document.getElementById('fNamaSekolah').value.trim();
  const jenjang= document.getElementById('fJenjangSekolah').value;
  const kota   = document.getElementById('fKotaSekolah').value.trim();

  if (!nama || !jenjang) {
    showSekolahAlert('danger', 'Nama sekolah dan jenjang wajib diisi.');
    return;
  }

  const payload  = { nama, jenjang, kota_kabupaten: kota };
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
    const data = await res.json();
    if (res.ok) { loadSekolahAdmin(); }
    else { alert(data.detail || 'Gagal menghapus sekolah.'); }
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

// =====================
// CLASSROOM SAYA — ROLE SEKOLAH (profil ringkas sekolah sendiri)
// =====================
async function loadProfilSekolah(sekolahId) {
  const profilBox = document.getElementById('profilSekolahBox');
  if (!profilBox) return;

  try {
    const res  = await fetch(`/api/classroom/${sekolahId}`);
    const data = await res.json();

    if (!res.ok) {
      profilBox.innerHTML = `<p class="text-danger small mb-0">Gagal memuat profil sekolah.</p>`;
      return;
    }

    const s = data.sekolah;
    const terisi = data.kelas.filter(k => k.classroom_url).length;
    profilBox.innerHTML = `
      <p class="mb-2"><span class="text-muted small d-block">Nama Sekolah</span><span class="fw-600">${s.nama}</span></p>
      <p class="mb-2"><span class="text-muted small d-block">Jenjang</span>
        <span class="badge rounded-pill badge-${s.jenjang}">${s.jenjang?.toUpperCase()}</span>
      </p>
      <p class="mb-2"><span class="text-muted small d-block">Kota/Kabupaten</span><span class="fw-600">${s.kota_kabupaten || '-'}</span></p>
      <p class="mb-0"><span class="text-muted small d-block">Kelas Terisi</span><span class="fw-600">${terisi} dari ${data.kelas.length} mata pelajaran</span></p>`;
  } catch {
    profilBox.innerHTML = `<p class="text-danger small mb-0">Gagal terhubung ke server.</p>`;
  }
}