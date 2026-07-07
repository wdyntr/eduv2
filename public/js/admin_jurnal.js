let jurnalFormModal = null;
let reviewJurnalModal = null;
let tolakJurnalModal = null;
let jurnalEditId = null; // null = ajukan baru, angka = revisi/resubmit
let jurnalTolakId = null;
let kategoriJurnalList = []; // daftar kategori valid dari tabel jurnal_kategori

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('modalFormJurnal')) {
    jurnalFormModal = new bootstrap.Modal(document.getElementById('modalFormJurnal'));
    loadKategoriJurnalForm();
  }

  if (JURNAL_ROLE === 'penulis') {
    if (document.getElementById('tabelJurnalSaya')) loadJurnalSaya();
    if (document.getElementById('statTotalUpload')) loadDashboardPenulis();
  } else {
    if (document.getElementById('modalReviewJurnal')) {
      reviewJurnalModal = new bootstrap.Modal(document.getElementById('modalReviewJurnal'));
      tolakJurnalModal = new bootstrap.Modal(document.getElementById('modalTolakJurnal'));
      loadJurnalPending();
    }
  }
});

async function loadKategoriJurnalForm() {
  try {
    const res = await fetch('/api/jurnal-kategori');
    const data = await res.json();
    kategoriJurnalList = data.items || [];
    const datalist = document.getElementById('kategoriJurnalOptions');
    if (datalist) datalist.innerHTML = kategoriJurnalList.map(k => `<option value="${k}">`).join('');
  } catch {}
}

function validasiKategoriJurnal() {
  const input = document.getElementById('fKategoriJurnal');
  const err = document.getElementById('errKategoriJurnal');
  if (!input) return true;

  const val = input.value.trim();
  const cocok = kategoriJurnalList.find(k => k.toLowerCase() === val.toLowerCase());

  if (val && !cocok) {
    input.value = '';
    input.classList.add('is-invalid');
    err?.classList.remove('d-none');
    return false;
  }

  if (cocok) input.value = cocok; // normalisasi ke penulisan asli di tabel
  input.classList.remove('is-invalid');
  err?.classList.add('d-none');
  return true;
}

const STATUS_LABEL = { pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' };

// =====================
// PENULIS
// =====================
async function loadJurnalSaya() {
  const tbody = document.getElementById('tabelJurnalSaya');
  if (!tbody) return;
  try {
    const res = await fetch('/api/admin/jurnal/mine');
    const data = await res.json();
    renderTabelJurnalSaya(data.items || []);
  } catch {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Gagal memuat data.</td></tr>`;
  }
}

function renderTabelJurnalSaya(items) {
  const tbody = document.getElementById('tabelJurnalSaya');
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Belum ada jurnal yang diajukan.</td></tr>`;
    return;
  }

  tbody.innerHTML = items.map((j, i) => `
    <tr>
      <td class="text-muted small">${i + 1}</td>
      <td style="max-width:280px">
        <div style="font-weight:600">${j.judul}</div>
        ${j.status === 'rejected' && j.catatan_admin ? `<div class="small text-danger mt-1"><i class="bi bi-info-circle me-1"></i>${j.catatan_admin}</div>` : ''}
      </td>
      <td class="text-muted small">${j.kategori}</td>
      <td><span class="badge rounded-pill badge-${j.status}">${STATUS_LABEL[j.status]}</span></td>
      <td class="text-muted small">${(j.created_at || '').slice(0, 10)}</td>
      <td>
        ${j.status === 'rejected'
          ? `<button class="btn btn-admin-edit btn-sm" onclick='showFormJurnal(${JSON.stringify(j)})'><i class="bi bi-arrow-repeat me-1"></i>Revisi</button>`
          : `<span class="text-muted small">-</span>`}
      </td>
    </tr>`).join('');
}

// =====================
// PENULIS — DASHBOARD (ringkasan)
// =====================
async function loadDashboardPenulis() {
  try {
    const res = await fetch('/api/admin/jurnal/mine');
    const data = await res.json();
    const items = data.items || [];

    const total = items.length;
    const approved = items.filter(j => j.status === 'approved').length;
    const pending = items.filter(j => j.status === 'pending').length;
    const rejected = items.filter(j => j.status === 'rejected');

    setText('statTotalUpload', total);
    setText('statApproved', approved);
    setText('statPending', pending);
    setText('statRejected', rejected.length);

    if (rejected.length) {
      document.getElementById('boxPerluRevisi').style.display = '';
      document.getElementById('listPerluRevisi').innerHTML = rejected.map(j => `
        <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
          <div>
            <div style="font-weight:600">${j.judul}</div>
            <div class="small text-danger mt-1"><i class="bi bi-info-circle me-1"></i>${j.catatan_admin || 'Tidak ada catatan.'}</div>
          </div>
          <a href="/admin/jurnal" class="btn btn-admin-edit btn-sm flex-shrink-0 ms-2">
            <i class="bi bi-arrow-repeat me-1"></i>Revisi
          </a>
        </div>`).join('');
    }

    const terbaru = [...items].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 5);
    const tbody = document.getElementById('tabelAktivitasTerbaru');
    if (tbody) {
      tbody.innerHTML = terbaru.length
        ? terbaru.map((j, i) => `
          <tr>
            <td class="text-muted small">${i + 1}</td>
            <td style="font-weight:600">${j.judul}</td>
            <td class="text-muted small">${j.kategori}</td>
            <td><span class="badge rounded-pill badge-${j.status}">${STATUS_LABEL[j.status]}</span></td>
            <td class="text-muted small">${(j.created_at || '').slice(0, 10)}</td>
          </tr>`).join('')
        : `<tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengajuan. Yuk ajukan jurnal pertamamu!</td></tr>`;
    }
  } catch {
    setText('statTotalUpload', '-');
    setText('statApproved', '-');
    setText('statPending', '-');
    setText('statRejected', '-');
  }
}

function setText(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}

function showFormJurnal(jurnal = null) {
  document.getElementById('jurnalAlert').classList.add('d-none');
  document.getElementById('fFileJurnal').value = '';
  document.getElementById('fFileBukti').value = '';
  document.getElementById('dzFileJurnalLabel').textContent = 'Klik untuk pilih file (maks. 10MB)';
  document.getElementById('dzFileBuktiLabel').textContent = 'Klik untuk pilih file (maks. 5MB)';
  document.getElementById('dzFileJurnal').classList.remove('has-file');
  document.getElementById('dzFileBukti').classList.remove('has-file');
  document.getElementById('fKategoriJurnal').classList.remove('is-invalid');
  document.getElementById('errKategoriJurnal')?.classList.add('d-none');

  const catatanBox = document.getElementById('jurnalCatatanBox');

  if (jurnal) {
    jurnalEditId = jurnal.id;
    document.getElementById('formJurnalTitle').textContent = 'Revisi & Ajukan Ulang';
    document.getElementById('fJudulJurnal').value = jurnal.judul;
    document.getElementById('fKategoriJurnal').value = jurnal.kategori;
    document.getElementById('fPenulisJurnal').value = jurnal.penulis;
    document.getElementById('fAbstrakJurnal').value = jurnal.abstrak || '';
    document.getElementById('fKataKunciJurnal').value = jurnal.kata_kunci || '';
    document.getElementById('fJumlahHalamanJurnal').value = jurnal.jumlah_halaman || '';
    document.getElementById('fTahunTerbitJurnal').value = jurnal.tahun_terbit || '';
    document.getElementById('fBahasaJurnal').value = jurnal.bahasa || 'Indonesia';
    document.getElementById('reqFileJurnal').textContent = '';
    document.getElementById('reqFileBukti').textContent = '';
    if (jurnal.catatan_admin) {
      catatanBox.textContent = 'Catatan admin: ' + jurnal.catatan_admin;
      catatanBox.classList.remove('d-none');
    }
  } else {
    jurnalEditId = null;
    document.getElementById('formJurnalTitle').textContent = 'Ajukan Jurnal Baru';
    document.getElementById('fJudulJurnal').value = '';
    document.getElementById('fKategoriJurnal').value = '';
    document.getElementById('fPenulisJurnal').value = '';
    document.getElementById('fAbstrakJurnal').value = '';
    document.getElementById('fKataKunciJurnal').value = '';
    document.getElementById('fJumlahHalamanJurnal').value = '';
    document.getElementById('fTahunTerbitJurnal').value = new Date().getFullYear();
    document.getElementById('fBahasaJurnal').value = 'Indonesia';
    document.getElementById('reqFileJurnal').textContent = '*';
    document.getElementById('reqFileBukti').textContent = '*';
    catatanBox.classList.add('d-none');
  }

  jurnalFormModal.show();
}

function updateDzLabel(inputId, labelId, dzId) {
  const input = document.getElementById(inputId);
  const label = document.getElementById(labelId);
  const dz = document.getElementById(dzId);
  if (input.files.length) {
    label.textContent = input.files[0].name;
    dz.classList.add('has-file');
  }
}

async function submitFormJurnal() {
  if (!validasiKategoriJurnal()) {
    showJurnalAlert('danger', 'Kategori tidak valid. Pilih salah satu kategori dari daftar.');
    return;
  }

  const judul = document.getElementById('fJudulJurnal').value.trim();
  const kategori = document.getElementById('fKategoriJurnal').value.trim();
  const penulis = document.getElementById('fPenulisJurnal').value.trim();
  const abstrak = document.getElementById('fAbstrakJurnal').value.trim();
  const kataKunci = document.getElementById('fKataKunciJurnal').value.trim();
  const jumlahHalaman = document.getElementById('fJumlahHalamanJurnal').value;
  const tahunTerbit = document.getElementById('fTahunTerbitJurnal').value;
  const bahasa = document.getElementById('fBahasaJurnal').value;
  const fileJurnal = document.getElementById('fFileJurnal').files[0];
  const fileBukti = document.getElementById('fFileBukti').files[0];

  if (!judul || !kategori || !penulis) {
    showJurnalAlert('danger', 'Judul, kategori, dan nama penulis wajib diisi.');
    return;
  }
  if (!jumlahHalaman || !tahunTerbit) {
    showJurnalAlert('danger', 'Jumlah halaman dan tahun terbit wajib diisi.');
    return;
  }
  if (!jurnalEditId && (!fileJurnal || !fileBukti)) {
    showJurnalAlert('danger', 'File jurnal dan bukti plagiarisme wajib diunggah.');
    return;
  }

  const fd = new FormData();
  fd.append('judul', judul);
  fd.append('kategori', kategori);
  fd.append('penulis', penulis);
  fd.append('abstrak', abstrak);
  fd.append('kata_kunci', kataKunci);
  fd.append('jumlah_halaman', jumlahHalaman);
  fd.append('tahun_terbit', tahunTerbit);
  fd.append('bahasa', bahasa);
  if (fileJurnal) fd.append('file_jurnal', fileJurnal);
  if (fileBukti) fd.append('file_bukti_plagiarisme', fileBukti);

  const url = jurnalEditId ? `/api/admin/jurnal/${jurnalEditId}/resubmit` : '/api/admin/jurnal';

  try {
    const res = await fetch(url, { method: 'POST', body: fd });
    if (res.ok) {
      jurnalFormModal.hide();
      loadJurnalSaya();
    } else {
      const data = await res.json();
      const pesan = data.errors ? Object.values(data.errors)[0][0] : (data.message || data.detail || 'Gagal mengirim pengajuan.');
      showJurnalAlert('danger', pesan);
    }
  } catch {
    showJurnalAlert('danger', 'Gagal terhubung ke server.');
  }
}

function showJurnalAlert(type, msg) {
  const el = document.getElementById('jurnalAlert');
  if (!el) return;
  el.className = `alert alert-${type} small py-2`;
  el.textContent = msg;
  el.classList.remove('d-none');
}

// =====================
// ADMIN — REVIEW
// =====================
function switchJurnalTab(tab, el) {
  document.querySelectorAll('#jurnalTabs .nav-link').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('tabPending').style.display = tab === 'pending' ? '' : 'none';
  document.getElementById('tabAll').style.display = tab === 'all' ? '' : 'none';
  const tabKategoriEl = document.getElementById('tabKategori');
  if (tabKategoriEl) tabKategoriEl.style.display = tab === 'kategori' ? '' : 'none';
  if (tab === 'all') loadJurnalAll();
  if (tab === 'kategori') loadKategoriAdmin();
}

async function loadJurnalPending() {
  const tbody = document.getElementById('tabelJurnalPending');
  try {
    const res = await fetch('/api/admin/jurnal/pending');
    const data = await res.json();
    renderTabelPending(data.items || []);
    document.getElementById('countPending').textContent = (data.items || []).length;
  } catch {
    if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Gagal memuat data.</td></tr>`;
  }
}

function renderTabelPending(items) {
  const tbody = document.getElementById('tabelJurnalPending');
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada pengajuan yang menunggu review.</td></tr>`;
    return;
  }

  tbody.innerHTML = items.map((j, i) => `
    <tr>
      <td class="text-muted small">${i + 1}</td>
      <td style="max-width:280px;font-weight:600">${j.judul}</td>
      <td class="text-muted small">${j.kategori}</td>
      <td class="text-muted small">${j.nama_pengaju || '-'}</td>
      <td class="text-muted small">${(j.created_at || '').slice(0, 10)}</td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn btn-admin-edit btn-sm" onclick='bukaReview(${JSON.stringify(j)})'><i class="bi bi-eye"></i></button>
          <button class="btn btn-sm" style="background:#e8f7ef;color:#1a7a4a" onclick="approveJurnal(${j.id})"><i class="bi bi-check-lg"></i></button>
          <button class="btn btn-admin-danger btn-sm" onclick="bukaTolak(${j.id})"><i class="bi bi-x-lg"></i></button>
        </div>
      </td>
    </tr>`).join('');
}

async function loadJurnalAll() {
  const tbody = document.getElementById('tabelJurnalAll');
  const status = document.getElementById('filterStatusJurnal')?.value || '';
  try {
    const res = await fetch(`/api/admin/jurnal/all${status ? '?status=' + status : ''}`);
    const data = await res.json();
    renderTabelAll(data.items || []);
  } catch {
    if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">Gagal memuat data.</td></tr>`;
  }
}

function renderTabelAll(items) {
  const tbody = document.getElementById('tabelJurnalAll');
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">Belum ada data jurnal.</td></tr>`;
    return;
  }

  tbody.innerHTML = items.map((j, i) => `
    <tr>
      <td class="text-muted small">${i + 1}</td>
      <td style="max-width:260px;font-weight:600">${j.judul}</td>
      <td class="text-muted small">${j.kategori}</td>
      <td class="text-muted small">${j.nama_pengaju || '-'}</td>
      <td><span class="badge rounded-pill badge-${j.status}">${STATUS_LABEL[j.status]}</span></td>
      <td class="text-muted small">${j.nama_reviewer || '-'}</td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn btn-admin-edit btn-sm" onclick='bukaReview(${JSON.stringify(j)})'><i class="bi bi-eye"></i></button>
          <button class="btn btn-admin-danger btn-sm" onclick="hapusJurnal(${j.id}, '${j.judul.replace(/'/g, "\\'")}')"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    </tr>`).join('');
}

function bukaReview(j) {
  document.getElementById('reviewJurnalBody').innerHTML = `
    <p class="mb-1"><strong>Judul:</strong> ${j.judul}</p>
    <p class="mb-1"><strong>Kategori:</strong> ${j.kategori}</p>
    <p class="mb-1"><strong>Penulis:</strong> ${j.penulis}</p>
    <p class="mb-1"><strong>Kata Kunci:</strong> ${j.kata_kunci || '-'}</p>
    <div class="row mb-2 small text-muted">
      <div class="col-6 col-md-3"><strong class="text-dark d-block">Halaman</strong>${j.jumlah_halaman || '-'}</div>
      <div class="col-6 col-md-3"><strong class="text-dark d-block">Tahun</strong>${j.tahun_terbit || '-'}</div>
      <div class="col-6 col-md-3"><strong class="text-dark d-block">Bahasa</strong>${j.bahasa || '-'}</div>
    </div>
    <p class="mb-3"><strong>Abstrak:</strong><br>${j.abstrak || '-'}</p>
    <a href="/uploads/jurnal/${j.file_jurnal}" target="_blank" class="btn btn-outline-secondary btn-sm me-2">
      <i class="bi bi-file-earmark-text me-1"></i>Lihat File Jurnal
    </a>
    <a href="/uploads/jurnal/${j.file_bukti_plagiarisme}" target="_blank" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-shield-check me-1"></i>Lihat Bukti Plagiarisme
    </a>

    <hr class="my-3">
    <p class="small fw-semibold mb-2"><i class="bi bi-pencil-square me-1"></i>Detail Publikasi (diisi admin)</p>
    <div class="row g-2">
      <div class="col-md-4">
        <label class="form-label small">Volume</label>
        <input type="text" id="rVolumeJurnal" class="form-control form-control-sm" value="${j.volume || ''}" placeholder="Contoh: Vol. 5">
      </div>
      <div class="col-md-4">
        <label class="form-label small">Nomor/Edisi</label>
        <input type="text" id="rNomorEdisiJurnal" class="form-control form-control-sm" value="${j.nomor_edisi || ''}" placeholder="Contoh: No. 2">
      </div>
      <div class="col-md-4">
        <label class="form-label small">ISSN</label>
        <input type="text" id="rIssnJurnal" class="form-control form-control-sm" value="${j.issn || ''}" placeholder="Contoh: 1234-5678">
      </div>
    </div>`;

  const footer = document.getElementById('reviewJurnalFooter');
  if (j.status === 'pending') {
    footer.innerHTML = `
      <button class="btn btn-admin-danger" onclick="reviewJurnalModal.hide(); bukaTolak(${j.id})"><i class="bi bi-x-lg me-1"></i>Tolak</button>
      <button class="btn btn-admin-primary" onclick="approveJurnal(${j.id}); reviewJurnalModal.hide()"><i class="bi bi-check-lg me-1"></i>Setujui</button>`;
  } else {
    footer.innerHTML = `
      <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
      <button class="btn btn-admin-primary" onclick="simpanDetailJurnal(${j.id})"><i class="bi bi-save me-1"></i>Simpan Detail</button>`;
  }
  reviewJurnalModal.show();
}

function ambilDetailPublikasiForm() {
  return {
    volume: document.getElementById('rVolumeJurnal')?.value.trim() || '',
    nomor_edisi: document.getElementById('rNomorEdisiJurnal')?.value.trim() || '',
    issn: document.getElementById('rIssnJurnal')?.value.trim() || '',
  };
}

async function approveJurnal(id) {
  if (!confirm('Setujui dan publikasikan jurnal ini?')) return;
  try {
    const res = await fetch(`/api/admin/jurnal/${id}/approve`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(ambilDetailPublikasiForm()),
    });
    if (res.ok) { loadJurnalPending(); if (document.getElementById('tabAll').style.display !== 'none') loadJurnalAll(); }
    else alert('Gagal menyetujui jurnal.');
  } catch { alert('Gagal terhubung ke server.'); }
}

async function simpanDetailJurnal(id) {
  try {
    const res = await fetch(`/api/admin/jurnal/${id}/detail`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(ambilDetailPublikasiForm()),
    });
    if (res.ok) {
      reviewJurnalModal.hide();
      if (document.getElementById('tabAll').style.display !== 'none') loadJurnalAll();
    } else {
      alert('Gagal menyimpan detail publikasi.');
    }
  } catch { alert('Gagal terhubung ke server.'); }
}

function bukaTolak(id) {
  jurnalTolakId = id;
  document.getElementById('fCatatanTolak').value = '';
  tolakJurnalModal.show();
}

async function submitTolakJurnal() {
  const catatan = document.getElementById('fCatatanTolak').value.trim();
  if (!catatan) { alert('Catatan wajib diisi agar penulis tahu apa yang perlu diperbaiki.'); return; }

  try {
    const res = await fetch(`/api/admin/jurnal/${jurnalTolakId}/reject`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ catatan }),
    });
    if (res.ok) {
      tolakJurnalModal.hide();
      loadJurnalPending();
      if (document.getElementById('tabAll').style.display !== 'none') loadJurnalAll();
    } else alert('Gagal menolak jurnal.');
  } catch { alert('Gagal terhubung ke server.'); }
}

async function hapusJurnal(id, judul) {
  if (!confirm(`Hapus jurnal "${judul}"? Tindakan ini tidak bisa dibatalkan.`)) return;
  try {
    const res = await fetch(`/api/admin/jurnal/${id}`, { method: 'DELETE' });
    if (res.ok) loadJurnalAll();
    else alert('Gagal menghapus jurnal.');
  } catch { alert('Gagal terhubung ke server.'); }
}

// =====================
// ADMIN — KELOLA KATEGORI
// =====================
async function loadKategoriAdmin() {
  const tbody = document.getElementById('tabelKategoriJurnal');
  try {
    const res = await fetch('/api/admin/jurnal-kategori');
    const data = await res.json();
    renderKategoriAdmin(data.items || []);
  } catch {
    if (tbody) tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">Gagal memuat data.</td></tr>`;
  }
}

function renderKategoriAdmin(items) {
  const tbody = document.getElementById('tabelKategoriJurnal');
  if (!tbody) return;

  if (!items.length) {
    tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori.</td></tr>`;
    return;
  }

  tbody.innerHTML = items.map((k, i) => `
    <tr>
      <td class="text-muted small">${i + 1}</td>
      <td style="font-weight:600">${k.nama}</td>
      <td class="text-muted small">${k.jumlah_jurnal} jurnal</td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn btn-admin-edit btn-sm" onclick="renameKategoriJurnal(${k.id}, '${k.nama.replace(/'/g, "\\'")}')"><i class="bi bi-pencil"></i></button>
          <button class="btn btn-admin-danger btn-sm" onclick="hapusKategoriJurnal(${k.id}, '${k.nama.replace(/'/g, "\\'")}')"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    </tr>`).join('');
}

function showKategoriAlert(type, msg) {
  const el = document.getElementById('kategoriAlert');
  if (!el) return;
  el.className = `alert alert-${type} py-2 small`;
  el.textContent = msg;
  el.classList.remove('d-none');
}

async function tambahKategoriJurnal() {
  const input = document.getElementById('fNamaKategoriBaru');
  const nama = input.value.trim();
  if (!nama) { showKategoriAlert('danger', 'Nama kategori wajib diisi.'); return; }

  try {
    const res = await fetch('/api/admin/jurnal-kategori', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nama }),
    });
    if (res.ok) {
      input.value = '';
      document.getElementById('kategoriAlert').classList.add('d-none');
      loadKategoriAdmin();
      loadKategoriJurnalForm();
    } else {
      const data = await res.json();
      showKategoriAlert('danger', data.errors?.nama?.[0] || data.detail || 'Gagal menambah kategori.');
    }
  } catch { showKategoriAlert('danger', 'Gagal terhubung ke server.'); }
}

async function renameKategoriJurnal(id, namaLama) {
  const namaBaru = prompt('Ubah nama kategori:', namaLama);
  if (!namaBaru || namaBaru.trim() === '' || namaBaru.trim() === namaLama) return;

  try {
    const res = await fetch(`/api/admin/jurnal-kategori/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nama: namaBaru.trim() }),
    });
    if (res.ok) { loadKategoriAdmin(); loadKategoriJurnalForm(); }
    else {
      const data = await res.json();
      alert(data.errors?.nama?.[0] || data.detail || 'Gagal mengubah kategori.');
    }
  } catch { alert('Gagal terhubung ke server.'); }
}

async function hapusKategoriJurnal(id, nama) {
  if (!confirm(`Hapus kategori "${nama}"?`)) return;
  try {
    const res = await fetch(`/api/admin/jurnal-kategori/${id}`, { method: 'DELETE' });
    if (res.ok) { loadKategoriAdmin(); loadKategoriJurnalForm(); }
    else {
      const data = await res.json();
      alert(data.detail || 'Gagal menghapus kategori.');
    }
  } catch { alert('Gagal terhubung ke server.'); }
}
