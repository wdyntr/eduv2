let jurnalPage = 1;
let jurnalKategoriAktif = '';
let jurnalSearchTimeout = null;

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('jurnalGrid')) {
    loadKategoriJurnal();
    loadJurnalPublik();
  }
});

async function loadKategoriJurnal() {
  try {
    const res = await fetch('/api/jurnal-kategori');
    const data = await res.json();
    const wrap = document.getElementById('kategoriChips');
    if (!wrap) return;

    (data.items || []).forEach(k => {
      const btn = document.createElement('button');
      btn.className = 'kategori-chip';
      btn.textContent = k;
      btn.dataset.kategori = k;
      btn.onclick = () => filterKategori(k, btn);
      wrap.appendChild(btn);
    });
  } catch {}
}

function filterKategori(kategori, el) {
  jurnalKategoriAktif = kategori;
  jurnalPage = 1;
  document.querySelectorAll('.kategori-chip').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
  loadJurnalPublik();
}

function debounceSearchJurnal() {
  clearTimeout(jurnalSearchTimeout);
  jurnalSearchTimeout = setTimeout(() => { jurnalPage = 1; loadJurnalPublik(); }, 400);
}

async function loadJurnalPublik() {
  const grid = document.getElementById('jurnalGrid');
  const count = document.getElementById('jurnalResultCount');
  if (grid) grid.innerHTML = `<div class="col-12 text-center py-5"><div class="spinner-border" style="color:#7b2fb5"></div></div>`;

  const q = document.getElementById('searchJurnal')?.value || '';
  const params = new URLSearchParams({
    limit: 9, page: jurnalPage,
    ...(jurnalKategoriAktif && { kategori: jurnalKategoriAktif }),
    ...(q && { q }),
  });

  try {
    const res = await fetch(`/api/jurnal?${params}`);
    const data = await res.json();
    renderJurnalGrid(data.items || []);
    if (count) count.textContent = `${data.total || 0} jurnal ditemukan`;
    renderJurnalPaginasi(data.total || 0, 9);
  } catch {
    if (grid) grid.innerHTML = `<div class="col-12 text-center text-muted py-5">Gagal memuat data jurnal.</div>`;
  }
}

function renderJurnalGrid(items) {
  const grid = document.getElementById('jurnalGrid');
  if (!grid) return;

  if (!items.length) {
    grid.innerHTML = `<div class="col-12 text-center text-muted py-5"><i class="bi bi-journal-x" style="font-size:2rem"></i><p class="mt-2">Belum ada jurnal yang dipublikasikan.</p></div>`;
    return;
  }

  grid.innerHTML = items.map(j => `
    <div class="col-md-6 col-lg-4">
      <a href="/jurnal/${j.id}" class="jurnal-card">
        <span class="jurnal-card-kategori">${j.kategori}</span>
        <h5>${j.judul}</h5>
        <p>${j.abstrak || 'Tidak ada abstrak.'}</p>
        <div class="jurnal-card-meta">
          <span><i class="bi bi-person me-1"></i>${j.penulis}</span>
          <span>${(j.created_at || '').slice(0, 10)}</span>
        </div>
      </a>
    </div>`).join('');
}

function renderJurnalPaginasi(total, perPage) {
  const wrap = document.getElementById('jurnalPaginasi');
  if (!wrap) return;
  const totalPages = Math.ceil(total / perPage);
  if (totalPages <= 1) { wrap.innerHTML = ''; return; }

  wrap.innerHTML = `
    <nav class="d-flex justify-content-center">
      <ul class="pagination">
        <li class="page-item ${jurnalPage === 1 ? 'disabled' : ''}">
          <button class="page-link" onclick="goPageJurnal(${jurnalPage - 1})"><i class="bi bi-chevron-left"></i></button>
        </li>
        ${Array.from({length: totalPages}, (_, i) => i + 1).map(p => `
          <li class="page-item ${p === jurnalPage ? 'active' : ''}">
            <button class="page-link" onclick="goPageJurnal(${p})">${p}</button>
          </li>`).join('')}
        <li class="page-item ${jurnalPage === totalPages ? 'disabled' : ''}">
          <button class="page-link" onclick="goPageJurnal(${jurnalPage + 1})"><i class="bi bi-chevron-right"></i></button>
        </li>
      </ul>
    </nav>`;
}

async function goPageJurnal(page) {
  jurnalPage = page;
  await loadJurnalPublik();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
