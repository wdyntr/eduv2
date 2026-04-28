// public/js/quiz.js

// ── THEME TOGGLE ──
function toggleTheme() {
  const html = document.documentElement;
  const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
  html.dataset.theme = next;
  localStorage.setItem('quiz-theme', next);
}

// Terapkan preferensi tersimpan saat halaman dimuat
(function () {
  const saved     = localStorage.getItem('quiz-theme');
  const preferred = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
  document.documentElement.dataset.theme = saved ?? preferred;
})();


// ── MENU (HAMBURGER DRAWER) ──
function openMenu() {
  const drawer  = document.getElementById('menu-drawer');
  const overlay = document.getElementById('menu-overlay');
  drawer.classList.add('open');
  drawer.setAttribute('aria-hidden', 'false');
  overlay.classList.add('show');
  document.body.style.overflow = 'hidden'; // cegah scroll background
}

function closeMenu() {
  const drawer  = document.getElementById('menu-drawer');
  const overlay = document.getElementById('menu-overlay');
  drawer.classList.remove('open');
  drawer.setAttribute('aria-hidden', 'true');
  overlay.classList.remove('show');
  document.body.style.overflow = '';
}


// ── STATE ──
let answered  = 0;
let selected  = {};
let submitted = false;

function startQuiz() {
  document.getElementById('screen-start').style.display  = 'none';
  document.getElementById('quiz-wrapper').style.display  = 'flex';
  document.getElementById('progress-wrap').style.display = 'flex';

  // Pindahkan nav ke drawer slot di mobile
  // Gunakan <= 768 agar lebih toleran terhadap berbagai ukuran mobile
  const nav  = document.getElementById('question-nav');
  const slot = document.getElementById('drawer-nav-slot');

  if (nav && slot) {
    if (window.innerWidth <= 640) {
      slot.appendChild(nav);
    }
  }
}

function selectOption(btn, questionId, answer) {
  if (submitted) return;

  const container = document.getElementById('opts-' + questionId);
  container.querySelectorAll('.option-btn').forEach(b => b.classList.remove('selected'));
  btn.classList.add('selected');

  const isNew = !selected[questionId];
  selected[questionId] = answer;

  if (isNew) {
    answered++;
    const pct = (answered / TOTAL) * 100;
    document.getElementById('progress-fill').style.width  = pct + '%';
    document.getElementById('progress-text').textContent  = answered + ' / ' + TOTAL;

    // Update badge hamburger: tampilkan sisa soal belum dijawab
    const remaining = TOTAL - answered;
    const badge     = document.getElementById('hamburger-badge');
    if (badge) {
      if (remaining > 0) {
        badge.textContent = remaining;
        badge.classList.add('show');
      } else {
        badge.classList.remove('show');
      }
    }
  }

  const navBtn = document.getElementById('nav-' + questionId);
  if (navBtn) navBtn.classList.add('answered');

  if (answered >= TOTAL) {
    const finish           = document.getElementById('btn-finish');
    finish.style.opacity   = '1';
    finish.style.pointerEvents = 'auto';

    // Sembunyikan badge saat semua soal selesai
    const badge = document.getElementById('hamburger-badge');
    if (badge) badge.classList.remove('show');
  }
}

async function submitQuiz() {
  if (submitted) return;
  if (Object.keys(selected).length < TOTAL) {
    alert('Jawab semua soal terlebih dahulu!');
    return;
  }

  submitted = true;
  document.getElementById('btn-finish').textContent = 'Memproses...';

  const response = await fetch(SUBMIT_URL, {
    method : 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body   : JSON.stringify({ answers: selected })
  });

  const data          = await response.json();
  let   correctCount  = 0;

  data.results.forEach(r => {
    const container = document.getElementById('opts-' + r.question_id);
    if (!container) return;

    container.querySelectorAll('.option-btn').forEach(b => {
      b.disabled = true;
      const optKey = b.querySelector('.option-key').textContent.trim();
      if (optKey === r.correct_answer)                   b.classList.add('correct');
      if (optKey === r.student_answer && !r.is_correct)  b.classList.add('wrong');
    });

    const fb = document.getElementById('fb-' + r.question_id);
    fb.classList.add('show', r.is_correct ? 'correct' : 'wrong');
    fb.querySelector('.feedback-title').textContent = r.is_correct ? 'Benar!' : 'Belum tepat';
    fb.querySelector('.feedback-text').textContent  = r.feedback;

    const navBtn = document.getElementById('nav-' + r.question_id);
    if (navBtn) {
      navBtn.classList.remove('answered');
      navBtn.classList.add(r.is_correct ? 'nav-correct' : 'nav-wrong');
    }

    if (r.is_correct) correctCount++;
  });

  setTimeout(() => showResult(correctCount, data.results), 1200);
}

function showResult(correctCount, results) {
  closeMenu();
  document.getElementById('quiz-wrapper').style.display  = 'none';
  document.getElementById('progress-wrap').style.display = 'none';
  document.getElementById('screen-result').style.display = 'flex';

  document.getElementById('score-num').textContent = correctCount;

  const titles = ['Coba Lagi!', 'Terus Berlatih!', 'Hampir!', 'Bagus!', 'Sempurna!'];
  const descs  = [
    'Jangan menyerah, coba pelajari lagi materinya.',
    'Kamu sudah cukup paham, tingkatkan lagi!',
    'Sedikit lagi sempurna!',
    'Kerja bagus, terus pertahankan!',
    'Luar biasa! Semua jawaban benar.'
  ];
  const idx = Math.min(Math.round((correctCount / TOTAL) * 4), 4);
  document.getElementById('result-title').textContent = titles[idx];
  document.getElementById('result-desc').textContent  = descs[idx];

  const list = document.getElementById('breakdown-list');
  list.innerHTML = '';
  results.forEach((r, i) => {
    const div       = document.createElement('div');
    div.className   = 'breakdown-item';
    div.innerHTML   = `
      <span class="q-text">Soal ${i + 1} — Jawaban: <strong>${r.student_answer}</strong>, Benar: <strong>${r.correct_answer}</strong></span>
      <span class="q-status ${r.is_correct ? 'ok' : 'no'}">${r.is_correct ? 'Benar' : 'Salah'}</span>
    `;
    list.appendChild(div);
  });
}

function scrollToQuestion(questionId) {
  const el = document.getElementById('question-' + questionId);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  closeMenu();
}