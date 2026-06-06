// =====================
// NAVBAR SCROLL EFFECT
// =====================
window.addEventListener('scroll', () => {
  const nav = document.getElementById('mainNav');
  if (window.scrollY > 50) {
    nav.classList.add('scrolled');
  } else {
    nav.classList.remove('scrolled');
  }
});

// =====================
// COUNTER ANIMATION
// =====================
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

const counters = document.querySelectorAll('.stat-num');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const target = parseInt(entry.target.dataset.count);
      animateCounter(entry.target, target);
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.5 });

counters.forEach(c => observer.observe(c));

// =====================
// LOAD MATERI TERBARU
// =====================
async function loadMateriTerbaru() {
  const container = document.getElementById('materiBaru');
  if (!container) return;

  try {
    const res  = await fetch('/api/materi?limit=6&sort=terbaru');
    const data = await res.json();

    if (!data.items || data.items.length === 0) {
      container.innerHTML = `
        <div class="col-12 text-center py-5">
          <p class="text-muted">Belum ada materi tersedia.</p>
        </div>`;
      return;
    }

    const thumbColors = {
      video: 'linear-gradient(135deg,#e8f7ef,#c5ebd8)',
      pdf:   'linear-gradient(135deg,#fff8e8,#faebc5)',
      ppt:   'linear-gradient(135deg,#e8f0ff,#c5d5f5)',
    };
    const thumbEmoji = { video: '🎬', pdf: '📄', ppt: '📑' };
    const badgeClass = { sma: 'badge-sma', smk: 'badge-smk', slb: 'badge-slb' };

    container.innerHTML = data.items.map(m => `
      <div class="col-md-6 col-lg-4">
        <a href="/media/${m.jenjang.toLowerCase()}/${m.id}" class="materi-card">
          <div class="materi-thumb" style="background:${thumbColors[m.tipe] || thumbColors.video}">
            ${thumbEmoji[m.tipe] || '📚'}
          </div>
          <div class="materi-body">
            <span class="badge rounded-pill ${badgeClass[m.jenjang.toLowerCase()] || 'badge-sma'} mb-2">
              ${m.jenjang}
            </span>
            <h6>${m.judul}</h6>
            <p>${m.deskripsi || ''}</p>
          </div>
          <div class="materi-footer">
            <span class="materi-type">${thumbEmoji[m.tipe]} ${m.tipe.toUpperCase()}</span>
            <span class="materi-cta">Buka →</span>
          </div>
        </a>
      </div>`).join('');

  } catch {
    container.innerHTML = `
      <div class="col-md-6 col-lg-4">
        <a href="#" class="materi-card">
          <div class="materi-thumb" style="background:linear-gradient(135deg,#e8f7ef,#c5ebd8)">🎬</div>
          <div class="materi-body">
            <span class="badge rounded-pill badge-sma mb-2">SMA</span>
            <h6>Limit dan Turunan Fungsi</h6>
            <p>Memahami konsep limit, turunan, dan penerapannya.</p>
          </div>
          <div class="materi-footer">
            <span class="materi-type">🎬 VIDEO</span>
            <span class="materi-cta">Buka →</span>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-lg-4">
        <a href="#" class="materi-card">
          <div class="materi-thumb" style="background:linear-gradient(135deg,#e8f0ff,#c5d5f5)">📑</div>
          <div class="materi-body">
            <span class="badge rounded-pill badge-smk mb-2">SMK</span>
            <h6>Bahasa Indonesia Profesi</h6>
            <p>Teknik penulisan laporan dan surat resmi.</p>
          </div>
          <div class="materi-footer">
            <span class="materi-type">📑 PPT</span>
            <span class="materi-cta">Buka →</span>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-lg-4">
        <a href="#" class="materi-card">
          <div class="materi-thumb" style="background:linear-gradient(135deg,#fff8e8,#faebc5)">📄</div>
          <div class="materi-body">
            <span class="badge rounded-pill badge-slb mb-2">SLB</span>
            <h6>Mengenal Makhluk Hidup</h6>
            <p>Materi adaptif dengan pendekatan visual.</p>
          </div>
          <div class="materi-footer">
            <span class="materi-type">📄 PDF</span>
            <span class="materi-cta">Buka →</span>
          </div>
        </a>
      </div>`;
  }
}

// =====================
// LOGIN ADMIN
// =====================
async function doLogin() {
  const username = document.getElementById('loginUsername')?.value?.trim();
  const password = document.getElementById('loginPassword')?.value;
  const btn      = document.getElementById('loginBtn');

  if (!username || !password) {
    showLoginAlert('Username dan password wajib diisi.');
    return;
  }

  btn.disabled  = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...';

  try {
    const res  = await fetch('/api/admin/login', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ username, password }),
    });
    const data = await res.json();

    if (res.ok) {
      window.location.href = '/admin';
    } else {
      showLoginAlert(data.detail || 'Username atau password salah.');
    }
  } catch {
    showLoginAlert('Gagal terhubung ke server.');
  } finally {
    btn.disabled  = false;
    btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Login';
  }
}

function showLoginAlert(msg) {
  const el = document.getElementById('loginAlert');
  if (!el) return;
  el.textContent = msg;
  el.classList.remove('d-none');
}

function togglePassword() {
  const input = document.getElementById('loginPassword');
  const icon  = document.getElementById('eyeIcon');
  if (!input) return;
  if (input.type === 'password') {
    input.type     = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    input.type     = 'password';
    icon.className = 'bi bi-eye';
  }
}

// =====================
// DOM READY
// =====================
document.addEventListener('DOMContentLoaded', () => {
  loadMateriTerbaru();

  // Auto buka modal login jika diarahkan dari halaman admin expired
  if (localStorage.getItem('openLogin') === '1') {
    localStorage.removeItem('openLogin');
    const modalEl = document.getElementById('modalLogin');
    if (modalEl) new bootstrap.Modal(modalEl).show();
  }

  document.getElementById('eyeBtn')?.addEventListener('click', togglePassword);

  document.getElementById('loginUsername')?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      const password = document.getElementById('loginPassword')?.value;
      if (!password) {
        document.getElementById('loginPassword').focus();
      } else {
        doLogin();
      }
    }
  });

  document.getElementById('loginPassword')?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') doLogin();
  });

  document.getElementById('modalLogin')?.addEventListener('hidden.bs.modal', () => {
    document.getElementById('loginUsername').value = '';
    document.getElementById('loginPassword').value = '';
    document.getElementById('loginPassword').type  = 'password';
    document.getElementById('eyeIcon').className   = 'bi bi-eye';
    document.getElementById('loginAlert').classList.add('d-none');
  });
});
