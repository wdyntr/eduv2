<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz Cerita — Template</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<!-- ══════════════ SCREEN: START ══════════════ -->
<section id="screen-start" class="screen active">
  <span class="hero-badge">Quiz Interaktif</span>
  <h1 class="hero-title anim">Misteri di <em>Hutan Tua</em></h1>
  <p class="hero-sub anim anim-d1">Sebuah perjalanan menguji pemahaman — baca setiap bagian cerita, lalu jawab pertanyaan yang tersembunyi di dalamnya.</p>

  <div class="hero-cover anim anim-d2">
    <!-- Ganti src dengan URL gambar cover Anda -->
    <img src="https://images.unsplash.com/photo-1448375240586-882707db888b?w=900&auto=format&fit=crop" alt="Hutan Tua" />
    <div class="cover-overlay"></div>
  </div>

  <div class="hero-meta anim anim-d3">
    <div class="meta-item">
      <span class="meta-val">2</span>
      <span class="meta-label">Babak</span>
    </div>
    <div class="meta-item">
      <span class="meta-val">4</span>
      <span class="meta-label">Pertanyaan</span>
    </div>
    <div class="meta-item">
      <span class="meta-val">~8</span>
      <span class="meta-label">Menit</span>
    </div>
  </div>

  <div class="anim anim-d4" style="display:flex; gap:12px; flex-wrap:wrap; justify-content:center;">
    <button class="btn-primary" onclick="startQuiz()">
      Mulai Petualangan
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
  </div>
</section>

<!-- ══════════════ PROGRESS BAR (shared) ══════════════ -->
<div class="progress-wrap" id="progress-wrap" style="display:none;">
  <button class="btn-ghost" onclick="goBack()" style="padding:6px 14px; font-size:13px;">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M13 8H3M7 4L3 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Kembali
  </button>
  <div class="progress-track">
    <div class="progress-fill" id="progress-fill" style="width:0%"></div>
  </div>
  <span class="progress-text" id="progress-text">0 / 4</span>
</div>

<!-- ══════════════ SCREEN: CHAPTER 1 ══════════════ -->
<section id="screen-ch1" class="screen" style="padding-top:0;">
  <div class="chapter-header anim">
    <p class="chapter-label">Babak 1</p>
    <h2 class="chapter-title">Jejak yang Ditinggalkan</h2>
  </div>

  <div class="story-block anim anim-d1">
    <!-- Ganti src dengan URL gambar babak 1 Anda -->
    <img class="story-image" src="https://images.unsplash.com/photo-1518495973542-4542c06a5843?w=900&auto=format&fit=crop" alt="Jejak di hutan" />
    <div class="story-text">
      <p>Langkah Rani terasa berat saat ia menerobos semak-semak yang basah oleh embun pagi. Hutan ini berbeda — bukan sekadar pohon dan daun, melainkan seolah punya napas sendiri. Angin berbisik di antara ranting, membawa aroma tanah dan kayu yang terbakar lama.</p>
      <p>Di tengah jalan setapak, ia menemukan sesuatu: sebuah kompas tua dengan jarum yang tidak menunjuk utara, melainkan berputar perlahan searah jarum jam.</p>
      <blockquote>"Siapa pun yang menemukan ini — jangan percaya arah yang kamu tahu."</blockquote>
      <p>Rani membaca tulisan di balik kompas itu dua kali. Tulisan tangan yang rapi, namun tintanya sudah pudar dimakan usia. Ia mendongak — cahaya matahari hanya bisa masuk melalui celah sempit di antara kanopi pohon.</p>
    </div>
  </div>

  <!-- QUESTION 1 -->
  <div class="question-block anim anim-d2">
    <p class="question-num">Pertanyaan 1 dari 4</p>
    <h3 class="question-text">Apa yang ditemukan Rani di tengah jalan setapak?</h3>
    <div class="options-list" id="opts-q1">
      <button class="option-btn" onclick="selectOption(this, 'q1', false)" data-q="q1">
        <span class="option-key">A</span>
        <span class="option-label">Sebuah peta kuno yang terlipat</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q1', true)" data-q="q1">
        <span class="option-key">B</span>
        <span class="option-label">Kompas tua dengan jarum berputar</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q1', false)" data-q="q1">
        <span class="option-key">C</span>
        <span class="option-label">Selembar kertas bertuliskan nama</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q1', false)" data-q="q1">
        <span class="option-key">D</span>
        <span class="option-label">Lampu senter yang masih menyala</span>
      </button>
    </div>
    <div class="feedback-box" id="fb-q1">
      <p class="feedback-title">—</p>
      <p id="fb-q1-text"></p>
    </div>
  </div>

  <!-- QUESTION 2 -->
  <div class="question-block anim anim-d3">
    <p class="question-num">Pertanyaan 2 dari 4</p>
    <h3 class="question-text">Apa pesan yang tertulis di balik kompas tersebut?</h3>
    <div class="options-list" id="opts-q2">
      <button class="option-btn" onclick="selectOption(this, 'q2', false)" data-q="q2">
        <span class="option-key">A</span>
        <span class="option-label">"Kembali sebelum malam tiba"</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q2', false)" data-q="q2">
        <span class="option-key">B</span>
        <span class="option-label">"Hutan ini memiliki rahasia besar"</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q2', true)" data-q="q2">
        <span class="option-key">C</span>
        <span class="option-label">"Jangan percaya arah yang kamu tahu"</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q2', false)" data-q="q2">
        <span class="option-key">D</span>
        <span class="option-label">"Tinggalkan ini dan jangan kembali"</span>
      </button>
    </div>
    <div class="feedback-box" id="fb-q2">
      <p class="feedback-title">—</p>
      <p id="fb-q2-text"></p>
    </div>
  </div>

  <div class="nav-row">
    <button class="btn-primary" onclick="goToChapter2()" id="btn-next-ch1" style="opacity:0.4; pointer-events:none;">
      Lanjut ke Babak 2
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
  </div>
</section>

<!-- ══════════════ SCREEN: CHAPTER 2 ══════════════ -->
<section id="screen-ch2" class="screen" style="padding-top:0;">
  <div class="chapter-header anim">
    <p class="chapter-label">Babak 2</p>
    <h2 class="chapter-title">Gua yang Berbicara</h2>
  </div>

  <div class="story-block anim anim-d1">
    <!-- Ganti src dengan URL gambar babak 2 Anda -->
    <img class="story-image" src="https://images.unsplash.com/photo-1504701954957-2010ec3bcec1?w=900&auto=format&fit=crop" alt="Gua misterius" />
    <div class="story-text">
      <p>Mengikuti jarum kompas yang berputar, Rani akhirnya tiba di mulut sebuah gua. Stalaktit menggantung seperti jari-jari raksasa, dan dari dalam terdengar suara gemericik air — atau mungkin suara langkah kaki yang jauh.</p>
      <p>Di dinding gua, seseorang telah mengukir gambar: seekor burung dengan tiga mata, terbang di atas pohon yang akarnya mencapai langit. Di bawah ukiran itu terdapat tanggal — tapi angkanya tidak berurutan.</p>
      <blockquote>"Bukan waktu yang menentukan jalan — melainkan keberanian untuk memilih."</blockquote>
      <p>Rani menyalakan senternya. Di ujung gua, cahaya lain berkelip — bukan dari pantulan, tapi seolah ada yang menyambut kedatangannya.</p>
    </div>
  </div>

  <!-- QUESTION 3 -->
  <div class="question-block anim anim-d2">
    <p class="question-num">Pertanyaan 3 dari 4</p>
    <h3 class="question-text">Apa yang terukir di dinding gua?</h3>
    <div class="options-list" id="opts-q3">
      <button class="option-btn" onclick="selectOption(this, 'q3', false)" data-q="q3">
        <span class="option-key">A</span>
        <span class="option-label">Peta menuju harta karun</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q3', false)" data-q="q3">
        <span class="option-key">B</span>
        <span class="option-label">Nama-nama orang yang pernah masuk</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q3', true)" data-q="q3">
        <span class="option-key">C</span>
        <span class="option-label">Burung bermata tiga di atas pohon terbalik</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q3', false)" data-q="q3">
        <span class="option-key">D</span>
        <span class="option-label">Petunjuk cara keluar dari hutan</span>
      </button>
    </div>
    <div class="feedback-box" id="fb-q3">
      <p class="feedback-title">—</p>
      <p id="fb-q3-text"></p>
    </div>
  </div>

  <!-- QUESTION 4 -->
  <div class="question-block anim anim-d3">
    <p class="question-num">Pertanyaan 4 dari 4</p>
    <h3 class="question-text">Apa yang Rani lihat di ujung gua?</h3>
    <div class="options-list" id="opts-q4">
      <button class="option-btn" onclick="selectOption(this, 'q4', false)" data-q="q4">
        <span class="option-key">A</span>
        <span class="option-label">Sebuah pintu tertutup dari batu</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q4', true)" data-q="q4">
        <span class="option-key">B</span>
        <span class="option-label">Cahaya yang berkelip seperti menyambut</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q4', false)" data-q="q4">
        <span class="option-key">C</span>
        <span class="option-label">Bayangan besar yang bergerak</span>
      </button>
      <button class="option-btn" onclick="selectOption(this, 'q4', false)" data-q="q4">
        <span class="option-key">D</span>
        <span class="option-label">Kolam air yang berkilau kebiruan</span>
      </button>
    </div>
    <div class="feedback-box" id="fb-q4">
      <p class="feedback-title">—</p>
      <p id="fb-q4-text"></p>
    </div>
  </div>

  <div class="nav-row">
    <button class="btn-primary" onclick="showResult()" id="btn-finish" style="opacity:0.4; pointer-events:none;">
      Lihat Hasil
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3l5 5-5 5M3 8h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
  </div>
</section>

<!-- ══════════════ SCREEN: RESULT ══════════════ -->
<section id="screen-result" class="screen">
  <div class="divider"></div>
  <div class="score-ring">
    <span class="score-num" id="score-num">0</span>
    <span class="score-denom" id="score-denom">dari 4</span>
  </div>
  <h2 class="result-title" id="result-title">Petualang Sejati</h2>
  <p class="result-desc" id="result-desc">Kamu memahami setiap detail cerita dengan baik. Mata dan pikiran yang tajam adalah bekal terbaik seorang penjelajah.</p>

  <div class="result-breakdown">
    <p class="breakdown-title">Ringkasan Jawaban</p>
    <div id="breakdown-list"></div>
  </div>

  <div class="result-actions">
    <button class="btn-primary" onclick="restartQuiz()">
      Main Lagi
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M14 8A6 6 0 1 1 8 2M8 2l3 3-3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <button class="btn-ghost" onclick="shareResult()">
      Bagikan
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><circle cx="12" cy="4" r="2" stroke="currentColor" stroke-width="1.5"/><circle cx="4" cy="8" r="2" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="1.5"/><path d="M6 7l4-2M6 9l4 2" stroke="currentColor" stroke-width="1.5"/></svg>
    </button>
  </div>
</section>

<script src="{{ asset('js/quiz.js') }}"></script>
</body>
</html>
