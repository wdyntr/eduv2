{{-- resources/views/quiz/partials/_progress.blade.php --}}
<div class="progress-wrap" id="progress-wrap" style="display:none;">
    <div class="progress-track">
        <div class="progress-fill" id="progress-fill" style="width:0%"></div>
    </div>
    <span class="progress-text" id="progress-text">0 / {{ $totalQuestions }}</span>
</div>