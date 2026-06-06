// State
let state = {
  jenjang:    JENJANG,       // dari template (diinjek server)
  tipe:       'semua',
  mapel:      'semua',
  sort:       'terbaru',
  q:          '',
  page:       1,
  perPage:    12,
  view:       'grid',
  total:      0,
};

// =====================
// INIT
// =====================
document.addEventListener('DOMContentLoaded', () => {
  loadMapelFilter();
  loadMateri();

  // View toggle buttons
  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => setView(btn.dataset.view));
  });

  // Sort
  document.getElementById('sortSelect')?.addEventListener('change', e => {
    state.sort = e.target.value;
    state.page = 1;
    loadMateri();
  });
});

// =====================
// LOAD MATA PELAJARAN FILTER
// =====================
async function loadMapelFilter() {
  const container = document.getElementById('filterMapel');
  if (!container) return;

  try {
    const res  = await fetch(`/api/mapel?jenjang=${state.jenjang}`);
    const data = await res.json();
    renderMapelFilter(data.items || []);
  } catch {
    // Placeholder mapel
    const placeholder = state.jenjang === 'sma'
      ? ['Matematika','Fisika','Kimia','Biologi','Bahasa Indonesia','Sejarah','Ekonomi']
      : state.jenjang === 'smk'
      ? ['Bahasa Indonesia','Matematika','Produktif Keahlian','IPA Terapan']
      : ['Matematika','Bahasa Indonesia','IPA','IPS','Seni Budaya'];
    renderMapelFilter(placeholder.map(n => ({ nama: n })));
  }
}

function renderMapelFilter(items) {
  const container = document.getElementById('filterMapel');
  if (!container) return;
  container.innerHTML = [
    { nama: 'Semua' },
    ...items,
  ].map(m => `
    <label class="filter-check">
      <input type="radio" name="mapel" value="${m.nama.toLowerCase()}"
        ${m.nama === 'Semua' ? 'checked' : ''}
        onchange="applyMapelFilter('${m.nama.toLowerCase()}')">
      ${m.nama}
    </label>`).join('');
}

// =====================
// FILTER HANDLERS
// =====================
function applyFilter() {
  state.tipe = document.querySelector('input[name="tipe"]:checked')?.value || 'semua';
  state.sort = document.getElementById('sortSelect')?.value || 'terbaru';
  state.page = 1;
  loadMateri();
}

function applyMapelFilter(mapel) {
  state.mapel = mapel;
  state.page  = 1;
  loadMateri();
}

function resetFilter() {
  state.tipe  = 'semua';
  state.mapel = 'semua';
  state.sort  = 'terbaru';
  state.q     = '';
  state.page  = 1;

  // Reset UI
  document.querySelector('input[name="tipe"][value="semua"]').checked = true;
  document.querySelector('input[name="mapel"][value="semua"]')?.click();
  document.getElementById('sortSelect').value = 'terbaru';
  document.getElementById('searchInput').value = '';

  loadMateri();
}

function doSearch() {
  state.q    = document.getElementById('searchInput')?.value || '';
  state.page = 1;
  loadMateri();
}

document.addEventListener('keydown', e => {
  if (e.key === 'Enter' && document.activeElement?.id === 'searchInput') doSearch();
});

// =====================
// VIEW TOGGLE
// =====================
function setView(view) {
  state.view = view;
  document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
  document.querySelector(`.view-btn[data-view="${view}"]`)?.classList.add('active');
  if (window._lastItems) renderItems(window._lastItems);
}

// =====================
// LOAD MATERI
// =====================
async function loadMateri() {
  const grid = document.getElementById('materiGrid');
  if (!grid) return;

  grid.innerHTML = `
    <div class="col-12 text-center py-5">
      <div class="spinner-border text-success"></div>
      <p class="mt-2 text-muted small">Memuat materi...</p>
    </div>`;

  const params = new URLSearchParams({
    jenjang:  state.jenjang,
    limit:    state.perPage,
    page:     state.page,
    sort:     state.sort,
    ...(state.tipe  !== 'semua' && { tipe: state.tipe }),
    ...(state.mapel !== 'semua' && { mapel: state.mapel }),
    ...(state.q && { q: state.q }),
  });

  try {
    const res  = await fetch(`/api/materi?${params}`);
    const data = await res.json();
    state.total = data.total || 0;
    window._lastItems = data.items || [];
    renderItems(window._lastItems);
    updateMeta(data);
    renderPagination(data.total || 0);
  } catch {
    window._lastItems = getPlaceholder();
    renderItems(window._lastItems);
    setText('resultCount', 'Menampilkan data contoh');
    setText('jenjangCountTotal', '-');
    setText('jenjangCountMapel', '-');
  }
}

// =====================
// RENDER ITEMS
// =====================
function renderItems(items) {
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

  const THUMB_BG    = { video:'linear-gradient(135deg,#e8f7ef,#c5ebd8)', ppt:'linear-gradient(135deg,#e8f0ff,#c5d5f5)', pdf:'linear-gradient(135deg,#fff8e8,#faebc5)' };
  const THUMB_EMOJI = { video:'🎬', ppt:'📑', pdf:'📄' };
  const BADGE_CLASS = { sma:'badge-sma', smk:'badge-smk', slb:'badge-slb' };
  const j = state.jenjang;

  if (state.view === 'grid') {
    grid.className = 'row g-3';
    grid.innerHTML = items.map(m => {
      const t = (m.tipe || 'video').toLowerCase();
      return `
        <div class="col-sm-6 col-xl-4">
          <a href="/media/${j}/${m.id}" class="materi-card">
            <div class="materi-thumb" style="background:${THUMB_BG[t] || THUMB_BG.video}">
              ${THUMB_EMOJI[t] || '📚'}
              <span class="tipe-badge">${t.toUpperCase()}</span>
            </div>
            <div class="materi-body">
              <span class="badge rounded-pill ${BADGE_CLASS[j] || 'badge-sma'} mb-1">${JENJANG_NAMA}</span>
              <h6>${m.judul}</h6>
              <p>${m.deskripsi || ''}</p>
            </div>
            <div class="materi-footer">
              <span class="materi-type">${THUMB_EMOJI[t]} ${m.mata_pelajaran || ''}</span>
              <span class="materi-cta">Buka →</span>
            </div>
          </a>
        </div>`;
    }).join('');
  } else {
    grid.className = 'row g-2';
    grid.innerHTML = items.map(m => {
      const t = (m.tipe || 'video').toLowerCase();
      return `
        <div class="col-12">
          <a href="/media/${j}/${m.id}" class="materi-list-item">
            <div class="materi-list-icon" style="background:${THUMB_BG[t] || THUMB_BG.video}">
              ${THUMB_EMOJI[t] || '📚'}
            </div>
            <div class="materi-list-body">
              <h6>${m.judul}</h6>
              <p>${m.deskripsi || ''}</p>
            </div>
            <div class="materi-list-meta">
              <span class="badge rounded-pill ${BADGE_CLASS[j] || 'badge-sma'}">${JENJANG_NAMA}</span>
              <span class="mapel">${m.mata_pelajaran || ''}</span>
            </div>
          </a>
        </div>`;
    }).join('');
  }
}

// =====================
// UPDATE META INFO
// =====================
function updateMeta(data) {
  const showing = Math.min(state.page * state.perPage, data.total || 0);
  setText('resultCount', `Menampilkan ${showing} dari ${data.total || 0} materi`);
  setText('jenjangCountTotal', data.total ?? '-');
  setText('jenjangCountMapel', data.total_mapel ?? '-');
}

function setText(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}

// =====================
// PAGINATION
// =====================
function renderPagination(total) {
  const wrap = document.getElementById('paginationWrap');
  const ul   = document.getElementById('pagination');
  if (!wrap || !ul) return;

  const totalPages = Math.ceil(total / state.perPage);
  if (totalPages <= 1) { wrap.style.display = 'none'; return; }

  wrap.style.display = 'block';
  const pages = [];

  // Prev
  pages.push(`
    <li class="page-item ${state.page === 1 ? 'disabled' : ''}">
      <button class="page-link" onclick="goPage(${state.page - 1})">
        <i class="bi bi-chevron-left"></i>
      </button>
    </li>`);

  // Numbers
  for (let i = 1; i <= totalPages; i++) {
    if (i === 1 || i === totalPages || Math.abs(i - state.page) <= 2) {
      pages.push(`
        <li class="page-item ${i === state.page ? 'active' : ''}">
          <button class="page-link" onclick="goPage(${i})">${i}</button>
        </li>`);
    } else if (Math.abs(i - state.page) === 3) {
      pages.push(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
    }
  }

  // Next
  pages.push(`
    <li class="page-item ${state.page === totalPages ? 'disabled' : ''}">
      <button class="page-link" onclick="goPage(${state.page + 1})">
        <i class="bi bi-chevron-right"></i>
      </button>
    </li>`);

  ul.innerHTML = pages.join('');
}

function goPage(page) {
  if (page < 1) return;
  state.page = page;
  loadMateri();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// =====================
// PLACEHOLDER
// =====================
function getPlaceholder() {
  const byJenjang = {
    sma: [
      { id:'#', tipe:'video', judul:'Limit dan Turunan Fungsi',  deskripsi:'Konsep limit, turunan.',     mata_pelajaran:'Matematika' },
      { id:'#', tipe:'pdf',   judul:'Fisika: Listrik Dinamis',   deskripsi:'Hukum Ohm dan rangkaian.',   mata_pelajaran:'Fisika' },
      { id:'#', tipe:'video', judul:'Kimia Organik Dasar',       deskripsi:'Struktur senyawa organik.',  mata_pelajaran:'Kimia' },
      { id:'#', tipe:'pdf',   judul:'Sejarah Indonesia Modern',  deskripsi:'Dari kemerdekaan ke kini.',  mata_pelajaran:'Sejarah' },
      { id:'#', tipe:'ppt',   judul:'Ekonomi: Pasar Modal',      deskripsi:'Investasi dan saham.',       mata_pelajaran:'Ekonomi' },
      { id:'#', tipe:'video', judul:'Biologi: Sel dan Jaringan', deskripsi:'Struktur sel makhluk hidup.',mata_pelajaran:'Biologi' },
    ],
    smk: [
      { id:'#', tipe:'ppt',   judul:'Bahasa Indonesia Profesi',  deskripsi:'Penulisan laporan resmi.',   mata_pelajaran:'Bahasa Indonesia' },
      { id:'#', tipe:'video', judul:'Matematika Terapan',        deskripsi:'Aplikasi matematika kerja.', mata_pelajaran:'Matematika' },
      { id:'#', tipe:'pdf',   judul:'IPA Terapan',               deskripsi:'Sains untuk industri.',      mata_pelajaran:'IPA Terapan' },
    ],
    slb: [
      { id:'#', tipe:'video', judul:'Mengenal Makhluk Hidup',    deskripsi:'Materi adaptif visual.',     mata_pelajaran:'IPA' },
      { id:'#', tipe:'pdf',   judul:'Matematika Dasar',          deskripsi:'Berhitung sederhana.',       mata_pelajaran:'Matematika' },
      { id:'#', tipe:'ppt',   judul:'Bahasa Indonesia Dasar',    deskripsi:'Membaca dan menulis.',       mata_pelajaran:'Bahasa Indonesia' },
    ],
  };
  return byJenjang[state.jenjang] || byJenjang.sma;
}
