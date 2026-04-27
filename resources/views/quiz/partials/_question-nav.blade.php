{{-- resources/views/quiz/partials/_question-nav.blade.php --}}
<div class="question-nav" id="question-nav">
    <p class="question-nav-title">Navigasi Soal</p>
    <div class="question-nav-grid">
        @php $qNum = 0; @endphp
        @foreach($questions as $question)
            @php $qNum++; @endphp
            <button
                class="nav-num"
                id="nav-{{ $question->id }}"
                onclick="scrollToQuestion({{ $question->id }})"
                title="Soal {{ $qNum }}">
                {{ $qNum }}
            </button>
        @endforeach
    </div>
</div>