// =====================
// SHARED HELPERS
// =====================
const THUMB_BG = {
  video: 'linear-gradient(135deg,#e8f7ef,#c5ebd8)',
  ppt:   'linear-gradient(135deg,#e8f0ff,#c5d5f5)',
};
const THUMB_EMOJI = { video: '🎬', ppt: '📑' };
const BADGE_CLASS  = { sma: 'badge-sma', smk: 'badge-smk', slb: 'badge-slb' };

// State
let currentView   = 'grid';   // 'grid' | 'list'
let currentTipe   = 'semua';
let currentJenjang= 'semua';
let currentPage   = 1;
const PER_PAGE    = 12;

// =====================
// VIEW TOGGLE
// =====================
function setView(view) {
  currentView = view;
  document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
  document.querySelector(`.view-btn[data-view="${view}"]`)?.classList.add('active');
  renderGrid(window._lastMateri || []);
}

// =====================
// FILTER TIPE
// =====================
function filterTipe(tipe, btn) {
  currentTipe = tipe;
  document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadMateri();
}

// =====================
// FILTER JENJANG (media landing)
// =====================
function filterJenjang(jenjang, el) {
  currentJenjang = jenjang;
  document.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  loadMateri();
}

// =====================
// SEARCH
// =====================
function doSearch() {
  currentPage = 1;
  loadMateri();
}

document.addEventListener('keydown', e => {
  if (e.key === 'Enter' && document.activeElement?.id === 'searchInput') doSearch();
});

// =====================
// LOAD MATERI
// =====================
async function loadMateri() {
  const grid = document.getElementById('materiGrid');
  if (!grid) return;

  const search  = document.getElementById('searchInput')?.value || '';
  const params  = new URLSearchParams({
    limit:   PER_PAGE,
    page:    currentPage,
    sort:    'terbaru',
    ...(currentTipe    !== 'semua' && { tipe: currentTipe }),
    ...(currentJenjang !== 'semua' && { jenjang: currentJenjang }),
    ...(search && { q: search }),
  });

  grid.innerHTML = `
    <div class="col-12 text-center py-5">
      <div class="spinner-border text-success"></div>
      <p class="mt-2 text-muted small">Memuat materi...</p>
    </div>`;

  try {
    const res  = await fetch(`/api/materi?${params}`);
    const data = await res.json();
    window._lastMateri = data.items || [];
    renderGrid(window._lastMateri);
    updateStats(data);
    updateJenjangMeta(data);
  } catch {
    window._lastMateri = getPlaceholder();
    renderGrid(window._lastMateri);
  }
}

// =====================
// RENDER GRID / LIST
// =====================
function renderGrid(items) {
  const grid = document.getElementById('materiGrid');
  if (!grid) return;

  if (!items.length) {
    grid.innerHTML = `
      <div class="col-12">
        <div class="empty-state">
          <div class="empty-icon">📭</div>
          <h5>Belum ada materi</h5>
          <p>Coba ubah filter atau kata kunci pencarian.</p>
        </div>
      </div>`;
    return;
  }

  if (currentView === 'grid') {
    grid.className = 'row g-3';
    grid.innerHTML = items.map(m => `
      <div class="col-sm-6 col-lg-4">
        ${cardGrid(m)}
      </div>`).join('');
  } else {
    grid.className = 'row g-2';
    grid.innerHTML = items.map(m => `
      <div class="col-12">
        ${cardList(m)}
      </div>`).join('');
  }
}

function cardGrid(m) {
  const t = (m.tipe || 'video').toLowerCase();
  const j = (m.jenjang || 'sma').toLowerCase();
  return `
    <a href="/media/${j}/${m.id}" class="materi-card">
      <div class="materi-thumb" style="background:${THUMB_BG[t] || THUMB_BG.video}">
        ${THUMB_EMOJI[t] || '📚'}
        <span class="tipe-badge">${t.toUpperCase()}</span>
      </div>
      <div class="materi-body">
        <span class="badge rounded-pill ${BADGE_CLASS[j] || 'badge-sma'}">${m.jenjang}</span>
        <h6>${m.judul}</h6>
        <p>${m.deskripsi || ''}</p>
      </div>
      <div class="materi-footer">
        <span class="materi-type">${THUMB_EMOJI[t]} ${m.mata_pelajaran || ''}</span>
        <span class="materi-cta">Buka →</span>
      </div>
    </a>`;
}

function cardList(m) {
  const t = (m.tipe || 'video').toLowerCase();
  const j = (m.jenjang || 'sma').toLowerCase();
  return `
    <a href="/media/${j}/${m.id}" class="materi-list-item">
      <div class="materi-list-icon" style="background:${THUMB_BG[t] || THUMB_BG.video}">
        ${THUMB_EMOJI[t] || '📚'}
      </div>
      <div class="materi-list-body">
        <h6>${m.judul}</h6>
        <p>${m.deskripsi || ''}</p>
      </div>
      <div class="materi-list-meta">
        <span class="badge rounded-pill ${BADGE_CLASS[j] || 'badge-sma'}">${m.jenjang}</span>
        <span class="mapel">${m.mata_pelajaran || ''}</span>
      </div>
    </a>`;
}

// =====================
// UPDATE STATS COUNTS
// =====================
function updateStats(data) {
  if (data.stats) {
    setText('countVideo', data.stats.video ?? '-');
    setText('countPpt',   data.stats.ppt   ?? '-');
    setText('countTotal', data.total       ?? '-');
  }
}

function updateJenjangMeta(data) {
  ['sma','smk','slb'].forEach(j => {
    const el = document.getElementById(`meta-${j}`);
    if (el && data.per_jenjang?.[j] !== undefined) {
      el.innerHTML = `<span><i class="bi bi-collection"></i> ${data.per_jenjang[j]} materi</span>`;
    }
  });
}

function setText(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}

// =====================
// PLACEHOLDER DATA
// =====================
function getPlaceholder() {
  return [
    { id:'#', jenjang:'SMA', tipe:'video', judul:'Limit dan Turunan Fungsi', deskripsi:'Konsep limit, turunan, dan penerapannya.', mata_pelajaran:'Matematika' },
    { id:'#', jenjang:'SMK', tipe:'ppt',   judul:'Bahasa Indonesia Profesi', deskripsi:'Teknik penulisan laporan resmi.',          mata_pelajaran:'Bahasa Indonesia' },
    { id:'#', jenjang:'SMA', tipe:'video', judul:'Kimia Organik Dasar',      deskripsi:'Struktur dan reaksi senyawa organik.',     mata_pelajaran:'Kimia' },
    { id:'#', jenjang:'SLB', tipe:'video', judul:'Mengenal Makhluk Hidup',   deskripsi:'Materi adaptif pendekatan visual.',        mata_pelajaran:'IPA' },
  ];
}

// =====================
// INIT
// =====================
document.addEventListener('DOMContentLoaded', () => {
  loadMateri();
});
