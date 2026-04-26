// public/js/quiz.js

function startQuiz() {
    document.getElementById('screen-start').style.display = 'none';
    document.getElementById('screen-quiz').style.display = 'block';
    document.getElementById('progress-wrap').style.display = 'flex';
}

async function selectOption(btn, questionId, answer, total) {
    const container = document.getElementById('opts-' + questionId);
    if (container.querySelector('.correct, .wrong')) return;

    // Disable semua opsi
    container.querySelectorAll('.option-btn').forEach(b => b.disabled = true);

    // Kirim ke server
    const response = await fetch(CHECK_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ question_id: questionId, answer: answer })
    });

    const data = await response.json();

    // Tandai jawaban
    if (data.correct) {
        btn.classList.add('correct');
    } else {
        btn.classList.add('wrong');
        // Tandai jawaban benar
        container.querySelectorAll('.option-btn').forEach(b => {
            if (b.querySelector('.option-key').textContent === data.correct_answer) {
                b.classList.add('correct');
            }
        });
    }

    // Tampilkan feedback
    const fb = document.getElementById('fb-' + questionId);
    fb.classList.add('show', data.correct ? 'correct' : 'wrong');
    fb.querySelector('.feedback-title').textContent = data.correct ? 'Benar!' : 'Belum tepat';
    fb.querySelector('.feedback-text').textContent = data.feedback;

    // Update progress
    answered++;
    results[questionId] = { correct: data.correct, feedback: data.feedback };
    const pct = (answered / TOTAL) * 100;
    document.getElementById('progress-fill').style.width = pct + '%';
    document.getElementById('progress-text').textContent = answered + ' / ' + TOTAL;

    // Aktifkan tombol selesai jika semua terjawab
    if (answered >= TOTAL) {
        const btn = document.getElementById('btn-finish');
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
    }
}

function showResult() {
    document.getElementById('screen-quiz').style.display = 'none';
    document.getElementById('screen-result').style.display = 'flex';

    const correct = Object.values(results).filter(r => r.correct).length;
    document.getElementById('score-num').textContent = correct;

    const titles = ['Coba Lagi!', 'Terus Berlatih!', 'Hampir!', 'Bagus!', 'Sempurna!'];
    const idx = Math.round((correct / TOTAL) * 4);
    document.getElementById('result-title').textContent = titles[idx];

    const list = document.getElementById('breakdown-list');
    list.innerHTML = '';
    Object.entries(results).forEach(([id, r]) => {
        const div = document.createElement('div');
        div.className = 'breakdown-item';
        div.innerHTML = `
            <span class="q-text">${r.feedback}</span>
            <span class="q-status ${r.correct ? 'ok' : 'no'}">${r.correct ? 'Benar' : 'Salah'}</span>
        `;
        list.appendChild(div);
    });
}