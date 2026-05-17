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

let syncDebounce = null;

// ── PILIH JAWABAN ──
function selectOption(btn, questionId, answer) {
    if (submitted) return;

    const container = document.getElementById('opts-' + questionId);
    container.querySelectorAll('.option-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    const isNew = !selected[questionId];
    selected[questionId] = answer;
    saveAnswers(); // simpan ke localStorage

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

    clearTimeout(syncDebounce);
    syncDebounce = setTimeout(syncAnswersToServer, 3000);
}

// ── PERIODIC SYNC — kirim ke server setiap 60 detik ──
let syncInterval = null;

async function syncAnswersToServer() {
    if (submitted || Object.keys(selected).length === 0) return;

    try {
        await fetch(SAVE_ANSWERS_URL, {
            method : 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({
                session_id: SESSION_ID,
                answers:    selected,
            })
        });
    } catch (e) {
        // Gagal sync — tidak masalah, coba lagi 60 detik berikutnya
        console.warn('Sync jawaban gagal, akan dicoba lagi.');
    }
}

function startSync() {
    syncInterval = setInterval(syncAnswersToServer, 30000); // setiap 60 detik
}

// Tambahkan setelah fungsi startSync()
window.addEventListener('beforeunload', () => {
    if (submitted || Object.keys(selected).length === 0) return;

    // ✅ Gunakan FormData agar _token terbaca Laravel
    const fd = new FormData();
    fd.append('_token', CSRF);
    fd.append('session_id', SESSION_ID);
    fd.append('answers', JSON.stringify(selected));

    navigator.sendBeacon(SAVE_ANSWERS_URL, fd);
});

// ── SUBMIT MANUAL ──
async function submitQuiz() {
    if (submitted) return;
    if (Object.keys(selected).length < TOTAL) {
        alert('Jawab semua soal terlebih dahulu!');
        return;
    }

    submitted = true;
    clearInterval(syncInterval); // stop periodic sync

    const btn = document.getElementById('btn-finish');
    btn.textContent   = 'Memproses...';
    btn.style.opacity = '0.6';

    // Sync sekali lagi sebelum submit final
    await syncAnswersToServer();

    try {
        const response = await fetch(SUBMIT_URL, {
            method : 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ answers: selected, session_id: SESSION_ID })
        });

        const data = await response.json();

        if (!response.ok && !data.already) {
            alert(data.error ?? 'Terjadi kesalahan, coba lagi.');
            submitted         = false;
            btn.textContent   = 'Kumpulkan Jawaban';
            btn.style.opacity = '1';
            startSync(); // hidupkan lagi sync
            return;
        }

        clearAnswers();
        clearInterval(timerInterval);
        window.location.href = RESULT_URL + '?session=' + SESSION_ID;

    } catch (e) {
        clearAnswers();
        window.location.href = RESULT_URL + '?session=' + SESSION_ID;
    }
}

// ── AUTO SUBMIT (waktu habis) ──
async function autoSubmit() {
    if (submitted) return;
    submitted = true;
    clearInterval(syncInterval);

    const btn = document.getElementById('btn-finish');
    if (btn) btn.textContent = 'Waktu habis...';

    // Ambil dari localStorage jika selected kosong
    if (Object.keys(selected).length === 0) {
        const saved = loadAnswers();
        if (saved && Object.keys(saved).length > 0) selected = saved;
    }

    // Sync dulu ke server
    await syncAnswersToServer();

    if (Object.keys(selected).length === 0) {
        // Tidak ada jawaban — scheduler yang handle
        clearAnswers();
        window.location.href = RESULT_URL + '?session=' + SESSION_ID;
        return;
    }

    try {
        const response = await fetch(SUBMIT_URL, {
            method : 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ answers: selected, session_id: SESSION_ID })
        });

        // Apapun hasilnya → ke result
        clearAnswers();
        clearInterval(timerInterval);
        window.location.href = RESULT_URL + '?session=' + SESSION_ID;

    } catch (e) {
        // Network error → ke result, scheduler yang handle
        clearAnswers();
        window.location.href = RESULT_URL + '?session=' + SESSION_ID;
    }
}

function scrollToQuestion(questionId) {
    const el = document.getElementById('question-' + questionId);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    closeMenu();
}

// ── TIMER ──
let timerInterval = null;

// Simpan deadline sebagai timestamp absolut
const DEADLINE_TS = Date.now() + (DURASI * 1000);

function startTimer() {
    if (typeof DURASI === 'undefined' || !DURASI) return;

    updateTimerFromDeadline(); // tampilkan langsung sebelum interval pertama

    timerInterval = setInterval(updateTimerFromDeadline, 1000);
}

function updateTimerFromDeadline() {
    // Hitung sisa waktu dari waktu sekarang vs deadline
    // Ini akurat meski tab tidak aktif / setInterval terlambat
    const remaining = Math.max(0, Math.floor((DEADLINE_TS - Date.now()) / 1000));

    updateTimerDisplay(remaining);

    // Warning state
    if (remaining <= 300 && remaining > 60) {
        document.getElementById('timer-wrap')?.classList.add('timer-warning');
        document.getElementById('timer-float')?.classList.add('timer-warning');
        document.getElementById('timer-wrap')?.classList.remove('timer-danger');
        document.getElementById('timer-float')?.classList.remove('timer-danger');
    }
    if (remaining <= 60) {
        document.getElementById('timer-wrap')?.classList.remove('timer-warning');
        document.getElementById('timer-float')?.classList.remove('timer-warning');
        document.getElementById('timer-wrap')?.classList.add('timer-danger');
        document.getElementById('timer-float')?.classList.add('timer-danger');
    }
    if (remaining <= 0) {
        clearInterval(timerInterval);
        autoSubmit();
    }
}

function updateTimerDisplay(seconds) {
    const h    = Math.floor(seconds / 3600);
    const m    = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
    const s    = (seconds % 60).toString().padStart(2, '0');
    const time = h > 0 ? `${h}:${m}:${s}` : `${m}:${s}`;

    const el      = document.getElementById('timer-text');
    const elFloat = document.getElementById('timer-text-float');
    if (el)      el.textContent      = time;
    if (elFloat) elFloat.textContent = time;
}
// ── ANSWER STORAGE ──
const ANSWER_KEY = 'quiz-answers-' + USER_ID + '-' + SESSION_ID;

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
        const finish = document.getElementById('btn-finish');
        finish.style.opacity     = '1';
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

function startSessionKeepalive() {
    setInterval(() => {
        fetch(window.location.href, {
            method: 'HEAD',
            credentials: 'same-origin',
        }).catch(() => {});
    }, 10 * 60 * 1000);
}

// ── INIT ──
restoreAnswers();
startTimer();
startSync(); // ← mulai periodic sync
startSessionKeepalive(); // ← tambah ini

if (typeof renderMathInElement !== 'undefined') {
    renderMath();
} else {
    document.addEventListener('DOMContentLoaded', renderMath);
}
