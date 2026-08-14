document.addEventListener('DOMContentLoaded', () => {
  loadSekolahKelas();
});

const JENJANG_ICON = { sma: '🎓', smk: '🔧', slb: '🌟' };

async function loadSekolahKelas() {
  const box = document.getElementById('profilSekolahDetailBox');
  const tbody = document.getElementById('tabelKelasMapel');

  try {
    const res = await fetch(
      `/api/sekolah/${SEKOLAH_ID}/kelas`
    );

    const data = await res.json();

    if (!res.ok) {
      throw new Error(
        data.detail ||
        data.message ||
        'Gagal memuat data.'
      );
    }

    renderProfilSekolah(data.sekolah);
    renderTabelKelas(data.kelas || []);

  } catch (err) {
    if (box) {
      box.innerHTML = `
        <p class="text-muted small mb-0">
          ${escapeHtmlKelas(err.message)}
        </p>`;
    }

    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="${KELAS_IS_SCOPED ? 8 : 7}"
              class="text-center text-muted py-4">
            ${escapeHtmlKelas(err.message)}
          </td>
        </tr>`;
    }
  }
}

function renderProfilSekolah(s) {
  const box = document.getElementById('profilSekolahDetailBox');
  if (!box) return;
  const j = (s.jenjang?.kode || s.jenjang || 'sma').toLowerCase();

  box.innerHTML = `
    <div class="d-flex align-items-center gap-3">
      <div class="admin-avatar" style="width:52px;height:52px;font-size:1.6rem">${JENJANG_ICON[j] || '🏫'}</div>
      <div>
        <div style="font-weight:700;font-size:1.05rem">${escapeHtmlKelas(s.nama)}</div>
        <div class="text-muted small">
          <span class="badge rounded-pill badge-${j} me-1">${j.toUpperCase()}</span>
          ${s.kota_kabupaten?.nama ? `<i class="bi bi-geo-alt me-1"></i>${escapeHtmlKelas(s.kota_kabupaten.nama)}` : ''}
        </div>
      </div>
    </div>`;
}

function renderTabelKelas(items) {
  const tbody = document.getElementById('tabelKelasMapel');
  if (!tbody) return;
  const colspan = KELAS_IS_SCOPED ? 8 : 7;

  if (!items.length) {
    tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-muted py-4">Belum ada mata pelajaran untuk jenjang ini. Tambahkan lewat menu Mata Pelajaran.</td></tr>`;
    return;
  }

  if (KELAS_IS_SCOPED) {
    tbody.innerHTML = items.map((k, i) => `
      <tr data-mapel-id="${k.mapel_id}">
        <td class="text-muted small">${i + 1}</td>
        <td style="font-weight:600">${k.nama}</td>
        <td>
          <input type="url" class="form-control form-control-sm input-url-kelas"
            value="${k.classroom_url || ''}" placeholder="https://classroom.google.com/c/...">
        </td>
        <td class="text-center">${k.jumlah_guru ?? '-'}</td>
        <td class="text-center">${k.jumlah_siswa ?? '-'}</td>
        <td class="text-center">${k.jumlah_task ?? '-'}</td>
        <td class="text-center">${k.jumlah_materi ?? '-'}</td>
        <td>
          <button class="btn btn-admin-primary btn-sm" onclick="simpanKelasMapel(${k.mapel_id}, this)">
            <i class="bi bi-check-lg"></i>
          </button>
        </td>
      </tr>`).join('');
    return;
  }

  // Role admin/dinas: hanya lihat, tanpa input maupun tombol aksi
  tbody.innerHTML = items.map((k, i) => `
    <tr>
      <td class="text-muted small">${i + 1}</td>
      <td style="font-weight:600">${k.nama}</td>
      <td>
        ${k.classroom_url
          ? `<a href="${k.classroom_url}" target="_blank" class="text-truncate d-inline-block" style="max-width:220px">${k.classroom_url}</a>`
          : `<span class="text-muted small">Belum diisi operator sekolah</span>`}
      </td>
      <td class="text-center">${k.jumlah_guru ?? '-'}</td>
      <td class="text-center">${k.jumlah_siswa ?? '-'}</td>
      <td class="text-center">${k.jumlah_task ?? '-'}</td>
      <td class="text-center">${k.jumlah_materi ?? '-'}</td>
    </tr>`).join('');
}

async function simpanKelasMapel(mapelId, btn) {
  const row = btn.closest('tr');
  const input = row.querySelector('.input-url-kelas');
  const url = input.value.trim();

  btn.disabled = true;
  try {
    const res = await fetch(
      `/api/sekolah/${SEKOLAH_ID}/kelas/${mapelId}`,
      {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          classroom_url: url || null,
        }),
      }
    );
    const data = await res.json();

    if (res.ok) {
      btn.innerHTML = '<i class="bi bi-check2"></i>';
      setTimeout(() => { btn.innerHTML = '<i class="bi bi-check-lg"></i>'; }, 1500);
    } else {
      alert(data.detail || 'Gagal menyimpan link. Pastikan URL valid.');
    }
  } catch {
    alert('Gagal terhubung ke server.');
  } finally {
    btn.disabled = false;
  }
}
function escapeHtmlKelas(value) {
  return String(value ?? '').replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  }[c]));
}
