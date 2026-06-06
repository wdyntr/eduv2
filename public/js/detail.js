const THUMB_BG    = { video: 'linear-gradient(135deg,#e8f7ef,#c5ebd8)', ppt: 'linear-gradient(135deg,#e8f0ff,#c5d5f5)', pdf: 'linear-gradient(135deg,#fff8e8,#faebc5)' };
const THUMB_EMOJI = { video: '🎬', ppt: '📑', pdf: '📄' };

document.addEventListener('DOMContentLoaded', () => {
  loadRelated();
});

async function loadRelated() {
  const list = document.getElementById('relatedList');
  if (!list) return;

  try {
    const res  = await fetch(`/api/materi?jenjang=${JENJANG}&mapel=${encodeURIComponent(MAPEL)}&limit=6`);
    const data = await res.json();
    const items = (data.items || []).filter(m => m.id !== MATERI_ID);

    if (!items.length) {
      list.innerHTML = `<p class="text-muted small text-center py-3">Belum ada materi terkait.</p>`;
      return;
    }

    list.innerHTML = items.map(m => {
      const t = (m.tipe || 'video').toLowerCase();
      return `
        <a href="/media/${JENJANG}/${m.id}" class="related-item">
          <div class="related-item-icon" style="background:${THUMB_BG[t] || THUMB_BG.video}">
            ${THUMB_EMOJI[t] || '📚'}
          </div>
          <div class="related-item-body">
            <h6>${m.judul}</h6>
            <p>${m.mata_pelajaran || ''} · ${t.toUpperCase()}</p>
          </div>
        </a>`;
    }).join('');

  } catch {
    list.innerHTML = `<p class="text-muted small text-center py-3">Gagal memuat materi terkait.</p>`;
  }
}
