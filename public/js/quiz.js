// public/js/quiz.js

(function () {
    const saved     = localStorage.getItem('quiz-theme');
    const preferred = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
    document.documentElement.dataset.theme = saved ?? preferred;

    if (window.innerWidth <= 640) {
        const nav  = document.getElementById('question-nav');
        const slot = document.getElementById('drawer-nav-slot');
        if (nav && slot) slot.appendChild(nav);

        const badge = document.getElementById('hamburger-badge');
        if (badge && typeof TOTAL !== 'undefined') {
            badge.textContent = TOTAL;
            badge.classList.add('show');
        }
    }
})();

// ── THEME TOGGLE ──
function toggleTheme() {
    const html = document.documentElement;
    const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
    html.dataset.theme = next;
    localStorage.setItem('quiz-theme', next);
}

// ── MENU ──
function openMenu() {
    const drawer  = document.getElementById('menu-drawer');
    const overlay = document.getElementById('menu-overlay');
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
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

function selectOption(btn, questionId, answer) {
    if (submitted) return;

    const container = document.getElementById('opts-' + questionId);
    container.querySelectorAll('.option-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    const isNew = !selected[questionId];
    selected[questionId] = answer;
    saveAnswers();

    if (isNew) {
        answered++;
        const pct = (answered / TOTAL) * 100;
        document.getElementById('progress-fill').style.width = pct + '%';
        document.getElementById('progress-text').textContent = answered + ' / ' + TOTAL;

        const remaining = TOTAL - answered;
        const badge = document.getElementById('hamburger-badge');
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
        const finish = document.getElementById('btn-finish');
        finish.style.opacity     = '1';
        finish.style.pointerEvents = 'auto';

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
    const btn = document.getElementById('btn-finish');
    btn.textContent  = 'Memproses...';
    btn.style.opacity = '0.6';

    const response = await fetch(SUBMIT_URL, {
        method : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body   : JSON.stringify({ answers: selected, session_id: SESSION_ID }) // ← tambah session_id
    });

    if (!response.ok) {
        const err = await response.json();
        alert(err.error ?? 'Terjadi kesalahan, coba lagi.');
        submitted        = false;
        btn.textContent  = 'Kumpulkan Jawaban';
        btn.style.opacity = '1';
        return;
    }

    clearAnswers();
    localStorage.removeItem('quiz-end-time-' + SESSION_ID);
    clearInterval(timerInterval);
    window.location.href = RESULT_URL + '?session=' + SESSION_ID;
}

function scrollToQuestion(questionId) {
    const el = document.getElementById('question-' + questionId);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    closeMenu();
}

// ── TIMER ──
let timerInterval = null;
let timeLeft      = 0;

function startTimer() {
    if (typeof DURASI === 'undefined' || !DURASI) return;

    timeLeft = DURASI; // ← langsung dari server, sudah akurat
    updateTimerDisplay(timeLeft);

    timerInterval = setInterval(() => {
        timeLeft--;
        updateTimerDisplay(timeLeft);

        if (timeLeft === 300) {
            document.getElementById('timer-wrap')?.classList.add('timer-warning');
            document.getElementById('timer-float')?.classList.add('timer-warning');
        }
        if (timeLeft === 60) {
            document.getElementById('timer-wrap')?.classList.add('timer-danger');
            document.getElementById('timer-float')?.classList.add('timer-danger');
        }
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            autoSubmit();
        }
    }, 1000);
}

function updateTimerDisplay(seconds) {
    const m    = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s    = (seconds % 60).toString().padStart(2, '0');
    const time = m + ':' + s;

    const el      = document.getElementById('timer-text');
    const elFloat = document.getElementById('timer-text-float');
    if (el)      el.textContent      = time;
    if (elFloat) elFloat.textContent = time;
}

async function autoSubmit() {
    if (submitted) return;
    submitted = true;

    const btn = document.getElementById('btn-finish');
    if (btn) btn.textContent = 'Waktu habis...';

    // Jika selected kosong (misal reload tepat saat waktu habis),
    // coba ambil dari localStorage sebelum menyerah
    if (Object.keys(selected).length === 0) {
        const saved = loadAnswers();
        if (saved && Object.keys(saved).length > 0) {
            selected = saved;
        }
    }

    // Jika masih kosong sama sekali — tidak ada yang bisa disubmit
    if (Object.keys(selected).length === 0) {
        clearAnswers();
        localStorage.removeItem('quiz-end-time-' + SESSION_ID);
        window.location.href = QUIZ_INDEX_URL;
        return;
    }

    const response = await fetch(SUBMIT_URL, {
        method : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body   : JSON.stringify({ answers: selected, session_id: SESSION_ID })
    });

    if (response.ok) {
        clearAnswers();
        localStorage.removeItem('quiz-end-time-' + SESSION_ID);
        clearInterval(timerInterval);
        window.location.href = RESULT_URL + '?session=' + SESSION_ID;
    } else {
        // Jika sudah submit sebelumnya (double submit), langsung ke result
        clearAnswers();
        localStorage.removeItem('quiz-end-time-' + SESSION_ID);
        window.location.href = RESULT_URL + '?session=' + SESSION_ID;
    }
}

// ── ANSWER STORAGE ──
const ANSWER_KEY = 'quiz-answers-' + SESSION_ID;

function saveAnswers() {
    localStorage.setItem(ANSWER_KEY, JSON.stringify(selected));
}

function loadAnswers() {
    const stored = localStorage.getItem(ANSWER_KEY);
    return stored ? JSON.parse(stored) : {};
}

function clearAnswers() {
    localStorage.removeItem(ANSWER_KEY);
}

function restoreAnswers() {
    const saved = loadAnswers();
    if (!saved || Object.keys(saved).length === 0) return;

    Object.entries(saved).forEach(([questionId, answer]) => {
        const container = document.getElementById('opts-' + questionId);
        if (!container) return;

        container.querySelectorAll('.option-btn').forEach(btn => {
            const optKey = btn.querySelector('.option-key').textContent.trim();
            if (optKey === answer) btn.classList.add('selected');
        });

        selected[questionId] = answer;
        answered++;

        const navBtn = document.getElementById('nav-' + questionId);
        if (navBtn) navBtn.classList.add('answered');
    });

    const pct = (answered / TOTAL) * 100;
    document.getElementById('progress-fill').style.width = pct + '%';
    document.getElementById('progress-text').textContent = answered + ' / ' + TOTAL;

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

    if (answered >= TOTAL) {
        const finish           = document.getElementById('btn-finish');
        finish.style.opacity   = '1';
        finish.style.pointerEvents = 'auto';
    }
}

// ── KATEX RENDER ──
function renderMath() {
    renderMathInElement(document.body, {
        delimiters: [
            { left: '$$', right: '$$', display: true  },
            { left: '$',  right: '$',  display: false },
        ],
        throwOnError: false,
        output: 'html',
    });
}

// ── INIT — urutan ini penting!
restoreAnswers(); // ← DULU restore jawaban dari localStorage
startTimer();     // ← BARU mulai timer (supaya autoSubmit punya data)

if (typeof renderMathInElement !== 'undefined') {
    renderMath();
} else {
    document.addEventListener('DOMContentLoaded', renderMath);
}
