let artikelTipe = '';
let artikelPageNum = 1;

document.addEventListener('DOMContentLoaded', () => {
  if (!ARTIKEL_KATEGORI_SLUG) loadKategoriGrid();
  loadArtikelList();
});

async function loadKategoriGrid() {
  const grid = document.getElementById('kategoriGrid');
  if (!grid) return;

  try {
    const res = await fetch('/api/artikel-kategori');
    const data = await res.json();
    const items = data.items || [];

    grid.innerHTML = items.length
      ? items.map(k => `
        <div class="col-6 col-md-4 col-lg-3">
          <a href="/artikel/kategori/${k.slug}" class="kategori-card">
            <div class="kategori-icon">${k.icon || '📚'}</div>
            <div class="kategori-nama">${k.nama}</div>
          </a>
        </div>`).join('')
      : `<div class="col-12 text-muted text-center py-3">Belum ada kategori.</div>`;
  } catch {
    grid.innerHTML = `<div class="col-12 text-muted text-center py-3">Gagal memuat kategori.</div>`;
  }
}

function filterTipeArtikel(tipe, btn) {
  artikelTipe = tipe;
  artikelPageNum = 1;
  document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadArtikelList();
}

async function loadArtikelList() {
  const grid = document.getElementById('artikelGrid');
  if (!grid) return;

  const params = new URLSearchParams({
    limit: 12,
    page: artikelPageNum,
    ...(ARTIKEL_KATEGORI_SLUG && { kategori: ARTIKEL_KATEGORI_SLUG }),
    ...(artikelTipe && { tipe: artikelTipe }),
  });

  grid.innerHTML = `<div class="col-12 text-center py-5"><div class="spinner-border text-success"></div></div>`;

  try {
    const res = await fetch(`/api/artikel?${params}`);
    const data = await res.json();
    const items = data.items || [];

    grid.innerHTML = items.length
      ? items.map(a => `
        <div class="col-sm-6 col-lg-4">
          <a href="/artikel/${a.slug}" class="artikel-card">
            <div class="artikel-thumb">
              ${a.thumbnail ? `<img src="${a.thumbnail}" alt="${a.judul}">` : (a.tipe === 'video' ? '🎬' : '📰')}
              ${a.tipe === 'video' ? '<span class="tipe-badge">VIDEO</span>' : ''}
            </div>
            <div class="artikel-body">
              ${a.kategori ? `<span class="badge rounded-pill bg-light text-dark border">${a.kategori}</span>` : ''}
              <h6>${a.judul}</h6>
            </div>
          </a>
        </div>`).join('')
      : `<div class="col-12 text-center text-muted py-5">Belum ada konten.</div>`;
  } catch {
    grid.innerHTML = `<div class="col-12 text-center text-muted py-5">Gagal memuat data.</div>`;
  }
}