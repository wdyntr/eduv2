document.addEventListener('DOMContentLoaded', () => {
  loadKelasSekolah();
});

async function loadKelasSekolah() {
  const grid = document.getElementById('mapelKelasGrid');

  try {
    const res = await fetch(`/api/classroom/${SEKOLAH_ID_DETAIL}`);
    const data = await res.json();
    if (!res.ok) throw new Error(data.detail || 'Gagal memuat data.');

    renderMapelKelas(data.kelas || []);
  } catch (err) {
    if (grid) grid.innerHTML = `<div class="col-12 text-center text-muted py-5">${err.message}</div>`;
  }
}

function renderMapelKelas(items) {
  const grid = document.getElementById('mapelKelasGrid');
  if (!grid) return;

  if (!items.length) {
    grid.innerHTML = `<div class="col-12 text-center text-muted py-5">Belum ada mata pelajaran untuk sekolah ini.</div>`;
    return;
  }

  grid.innerHTML = items.map(k => {
    const hasUrl = k.classroom_url && k.classroom_url.trim() !== '';
    return `
    <div class="col-md-6 col-lg-4">
      <div class="mapel-kelas-card">
        <h6>${k.nama}</h6>
        ${hasUrl
          ? `<a href="${k.classroom_url}" target="_blank" rel="noopener" class="btn-classroom flex-shrink-0">
              <i class="bi bi-box-arrow-up-right"></i> Masuk
             </a>`
          : `<span class="btn-classroom disabled flex-shrink-0">
              <i class="bi bi-clock"></i> Belum Ada
             </span>`}
      </div>
    </div>`;
  }).join('');
}