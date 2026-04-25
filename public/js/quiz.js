const state = {
  answers: {},   // {q1: true/false, ...}
  feedbacks: {
    q1: { correct: "Benar! Rani menemukan kompas tua dengan jarum yang berputar searah jarum jam, bukan menunjuk utara.", wrong: "Belum tepat. Kembali ke paragraf kedua — Rani menemukan kompas, bukan peta." },
    q2: { correct: "Tepat sekali! Pesan itu berbunyi: \"Jangan percaya arah yang kamu tahu.\"", wrong: "Coba lagi. Perhatikan kutipan yang ada dalam cerita." },
    q3: { correct: "Benar! Ukiran menggambarkan seekor burung bermata tiga, terbang di atas pohon yang akarnya mencapai langit.", wrong: "Kurang tepat. Baca ulang deskripsi dinding gua." },
    q4: { correct: "Tepat! Rani melihat cahaya yang berkelip di ujung gua, seolah ada yang menyambut kedatangannya.", wrong: "Belum benar. Perhatikan kalimat terakhir dalam cerita babak 2." }
  },
  totalQuestions: 4
};

function updateProgress() {
  const answered = Object.keys(state.answers).length;
  const pct = (answered / state.totalQuestions) * 100;
  document.getElementById('progress-fill').style.width = pct + '%';
  document.getElementById('progress-text').textContent = answered + ' / ' + state.totalQuestions;
}

function selectOption(btn, qId, isCorrect) {
  const container = document.getElementById('opts-' + qId);
  if (container.querySelector('.correct, .wrong')) return; // already answered

  const allBtns = container.querySelectorAll('.option-btn');
  allBtns.forEach(b => b.disabled = true);

  state.answers[qId] = isCorrect;

  if (isCorrect) {
    btn.classList.add('correct');
  } else {
    btn.classList.add('wrong');
    // show correct answer
    allBtns.forEach(b => {
      if (b.onclick && b.onclick.toString().includes('true')) b.classList.add('correct');
    });
  }

  const fb = document.getElementById('fb-' + qId);
  const fbText = document.getElementById('fb-' + qId + '-text');
  fb.classList.add('show', isCorrect ? 'correct' : 'wrong');
  fb.querySelector('.feedback-title').textContent = isCorrect ? 'Benar!' : 'Belum tepat';
  fbText.textContent = isCorrect ? state.feedbacks[qId].correct : state.feedbacks[qId].wrong;

  updateProgress();
  checkChapterComplete();
}

function checkChapterComplete() {
  const ch1done = state.answers['q1'] !== undefined && state.answers['q2'] !== undefined;
  const ch2done = state.answers['q3'] !== undefined && state.answers['q4'] !== undefined;
  const btn1 = document.getElementById('btn-next-ch1');
  const btn2 = document.getElementById('btn-finish');
  if (btn1 && ch1done) { btn1.style.opacity = '1'; btn1.style.pointerEvents = 'auto'; }
  if (btn2 && ch2done) { btn2.style.opacity = '1'; btn2.style.pointerEvents = 'auto'; }
}

function showScreen(id) {
  document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function startQuiz() {
  document.getElementById('progress-wrap').style.display = 'flex';
  showScreen('screen-ch1');
  updateProgress();
}

function goToChapter2() {
  showScreen('screen-ch2');
}

function goBack() {
  const active = document.querySelector('.screen.active');
  if (active.id === 'screen-ch1') { document.getElementById('progress-wrap').style.display='none'; showScreen('screen-start'); }
  else if (active.id === 'screen-ch2') showScreen('screen-ch1');
  else if (active.id === 'screen-result') showScreen('screen-ch2');
}

function showResult() {
  const total = state.totalQuestions;
  const correct = Object.values(state.answers).filter(v => v).length;
  document.getElementById('score-num').textContent = correct;
  document.getElementById('score-denom').textContent = 'dari ' + total;

  const titles = ['Hm, Coba Lagi!', 'Terus Berlatih!', 'Hampir Sempurna!', 'Detektif Andal!', 'Penguasa Cerita!'];
  const descs = [
    'Jangan menyerah. Setiap penjelajah butuh waktu untuk memahami hutan ini.',
    'Kamu sudah memahami sebagian besar cerita. Baca ulang bagian yang terlewat!',
    'Sangat bagus! Hanya satu detail yang luput dari perhatianmu.',
    'Luar biasa! Kamu membaca dengan sangat teliti.',
    'Sempurna! Tidak ada satu pun yang tersembunyi dari matamu.'
  ];

  const idx = Math.min(correct, 4);
  document.getElementById('result-title').textContent = titles[idx];
  document.getElementById('result-desc').textContent = descs[idx];

  const questions = [
    'Apa yang ditemukan Rani di jalan setapak?',
    'Apa pesan di balik kompas?',
    'Apa yang terukir di dinding gua?',
    'Apa yang dilihat Rani di ujung gua?'
  ];
  const qKeys = ['q1','q2','q3','q4'];
  const list = document.getElementById('breakdown-list');
  list.innerHTML = '';
  qKeys.forEach((k,i) => {
    const ok = state.answers[k] === true;
    const div = document.createElement('div');
    div.className = 'breakdown-item';
    div.innerHTML = `<span class="q-text">${questions[i]}</span><span class="q-status ${ok ? 'ok' : 'no'}">${ok ? 'Benar' : 'Salah'}</span>`;
    list.appendChild(div);
  });

  showScreen('screen-result');
}

function restartQuiz() {
  state.answers = {};
  document.querySelectorAll('.option-btn').forEach(b => {
    b.classList.remove('selected','correct','wrong');
    b.disabled = false;
  });
  document.querySelectorAll('.feedback-box').forEach(fb => {
    fb.classList.remove('show','correct','wrong');
  });
  ['btn-next-ch1','btn-finish'].forEach(id => {
    const b = document.getElementById(id);
    if (b) { b.style.opacity = '0.4'; b.style.pointerEvents = 'none'; }
  });
  updateProgress();
  document.getElementById('progress-wrap').style.display = 'none';
  showScreen('screen-start');
}

function shareResult() {
  const correct = Object.values(state.answers).filter(v => v).length;
  const text = `Aku mendapat ${correct}/${state.totalQuestions} di Quiz Cerita "Misteri di Hutan Tua"! Coba kamu juga! 🌿`;
  if (navigator.share) {
    navigator.share({ text });
  } else if (navigator.clipboard) {
    navigator.clipboard.writeText(text).then(() => alert('Disalin ke clipboard!'));
  }
}