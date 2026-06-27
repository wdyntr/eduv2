// Counter animation
function animateCounter(el, target, duration = 2000) {
  let start = 0;
  const step = target / (duration / 16);
  const timer = setInterval(() => {
    start += step;
    if (start >= target) {
      el.textContent = target.toLocaleString('id-ID');
      clearInterval(timer);
    } else {
      el.textContent = Math.floor(start).toLocaleString('id-ID');
    }
  }, 16);
}

// Load materi terbaru dari API
async function loadMateriTerbaru() {
  const container = document.getElementById('materiBaru');
  if (!container) return;

  try {
    const res = await fetch('/api/materi?limit=6&sort=terbaru');
    const data = await res.json();

    if (!data.items || data.items.length === 0) {
      container.innerHTML = renderPlaceholder();
      return;
    }

    container.innerHTML = data.items.map(m => renderMateriCard(m)).join('');
  } catch {
    container.innerHTML = renderPlaceholder();
  }
}

function renderMateriCard(m) {
  const thumbBg = {
    video: 'linear-gradient(135deg,#e8f7ef,#c5ebd8)',
    ppt:   'linear-gradient(135deg,#e8f0ff,#c5d5f5)',
  };
  const emoji = { video: '🎬', ppt: '📑' };
  const badge = { sma: 'badge-sma', smk: 'badge-smk', slb: 'badge-slb' };
  const j = (m.jenjang || 'sma').toLowerCase();
  const t = (m.tipe || 'video').toLowerCase();

  const thumbInner = m.thumbnail
    ? `<img src="${m.thumbnail}" alt="${m.judul}" class="materi-thumb-img" loading="lazy">`
    : `${emoji[t] || '📚'}`;

  return `
    <div class="col-md-6 col-lg-4">
      <a href="/media/${j}/${m.id}" class="materi-card">
        <div class="materi-thumb" style="background:${thumbBg[t] || thumbBg.video}">
          ${thumbInner}
          <span class="tipe-badge">${t.toUpperCase()}</span>
        </div>
        <div class="materi-body">
          <span class="badge rounded-pill ${badge[j] || 'badge-sma'} mb-2">${m.jenjang}</span>
          <h6>${m.judul}</h6>
          <p>${m.deskripsi || ''}</p>
        </div>
        <div class="materi-footer">
          <span class="materi-type">${emoji[t]} ${m.mata_pelajaran || ''}</span>
          <span class="materi-cta">Buka →</span>
        </div>
      </a>
    </div>`;
}

function renderPlaceholder() {
  const items = [
    { jenjang: 'SMA', tipe: 'video', judul: 'Limit dan Turunan Fungsi', desc: 'Konsep limit, turunan, dan penerapannya.' },
    { jenjang: 'SMK', tipe: 'ppt',   judul: 'Bahasa Indonesia Profesi', desc: 'Teknik penulisan laporan resmi.' },
  ];
  return items.map(i => renderMateriCard({ ...i, id: '#', deskripsi: i.desc })).join('');
}

document.addEventListener('DOMContentLoaded', loadMateriTerbaru);
