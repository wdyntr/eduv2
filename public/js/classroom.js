let currentJenjang = 'semua';

async function loadClassroom() {
  const grid   = document.getElementById('classroomGrid');
  const empty  = document.getElementById('emptyState');
  const search = document.getElementById('searchInput')?.value || '';
  const sort   = document.getElementById('sortSelect')?.value || 'az';

  if (grid) grid.innerHTML = `
    <div class="col-12 text-center py-5">
      <div class="spinner-border text-success"></div>
      <p class="mt-2 text-muted small">Memuat data sekolah...</p>
    </div>`;
  if (empty) empty.classList.add('d-none');

  const params = new URLSearchParams({
    ...(search                     && { search }),
    ...(currentJenjang !== 'semua' && { jenjang: currentJenjang }),
    ...(sort                       && { sort }),
  });

  try {
    const res   = await fetch(`/api/classroom?${params}`);
    const data  = await res.json();
    const items = data.items || [];

    setText('resultCount', `Menampilkan ${items.length} sekolah`);

    if (!items.length) {
      if (grid)  grid.innerHTML = '';
      if (empty) empty.classList.remove('d-none');
    } else {
      if (grid) grid.innerHTML = items.map(s => schoolCard(s)).join('');
    }
  } catch {
    if (grid) grid.innerHTML = `
      <div class="col-12 text-center text-muted py-5">
        <p>Gagal memuat data sekolah.</p>
      </div>`;
  }
}

async function loadStats() {
  try {
    const res   = await fetch('/api/classroom');
    const data  = await res.json();
    const items = data.items || [];

    const count = { sma: 0, smk: 0, slb: 0 };
    items.forEach(s => {
      const j = (s.jenjang || '').toLowerCase();
      if (count[j] !== undefined) count[j]++;
    });

    setText('countSma',   count.sma);
    setText('countSmk',   count.smk);
    setText('countSlb',   count.slb);
    setText('countTotal', items.length);
  } catch {
    ['countSma','countSmk','countSlb','countTotal'].forEach(id => setText(id, '-'));
  }
}

function schoolCard(s) {
  const j      = (s.jenjang || 'sma').toLowerCase();
  const icons  = { sma: '🎓', smk: '🔧', slb: '🌟' };
  const icon   = icons[j] || '🏫';
  const hasUrl = s.classroom_url && s.classroom_url.trim() !== '';

  return `
    <div class="col-md-6 col-lg-4">
      <div class="school-card">
        <div class="school-card-header">
          <div class="school-avatar ${j}">${icon}</div>
          <div>
            <div class="school-name">${s.nama}</div>
            <div class="school-kota">
              <i class="bi bi-geo-alt"></i> ${s.kota_kabupaten || 'Lampung'}
            </div>
          </div>
        </div>
        <div>
          <span class="badge rounded-pill badge-${j} mb-2">${j.toUpperCase()}</span>
        </div>
        <div class="school-card-footer">
          ${hasUrl
            ? `<a href="${s.classroom_url}" target="_blank" rel="noopener" class="btn-classroom">
                <i class="bi bi-box-arrow-up-right"></i> Buka Classroom
               </a>`
            : `<span class="btn-classroom disabled">
                <i class="bi bi-clock"></i> Belum Tersedia
               </span>`}
        </div>
      </div>
    </div>`;
}

function filterJenjang(jenjang, el) {
  currentJenjang = jenjang;
  document.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  loadClassroom();
}

function doSearch() {
  loadClassroom();
}

document.addEventListener('keydown', e => {
  if (e.key === 'Enter' && document.activeElement?.id === 'searchInput') doSearch();
});

function setText(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}

document.addEventListener('DOMContentLoaded', () => {
  loadStats();
  loadClassroom();
});
