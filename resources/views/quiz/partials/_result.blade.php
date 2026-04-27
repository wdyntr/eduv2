{{-- resources/views/quiz/partials/_result.blade.php --}}
<section id="screen-result" class="screen" style="display:none;">
    <div class="divider"></div>
    <div class="score-ring">
        <span class="score-num" id="score-num">0</span>
        <span class="score-denom" id="score-denom">dari {{ $totalQuestions }}</span>
    </div>
    <h2 class="result-title" id="result-title">—</h2>
    <p class="result-desc" id="result-desc"></p>

    <div class="result-breakdown">
        <p class="breakdown-title">Ringkasan Jawaban</p>
        <div id="breakdown-list"></div>
    </div>

    <div class="result-actions">
        <button class="btn-primary" onclick="location.reload()">
            Main Lagi
        </button>
    </div>
</section>