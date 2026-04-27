{{-- resources/views/quiz/partials/_questions.blade.php --}}
<section id="screen-quiz" class="screen-quiz">

    @php $qNum = 0; @endphp

    @foreach($questions as $question)
        @php $qNum++; @endphp

        {{-- Teks bacaan --}}
        @if($loop->first || $question->passage_id !== $questions[$loop->index - 1]->passage_id)
        <div class="story-block anim">
            <div class="story-text">
                {!! $question->passage_highlighted
                    ?? ($question->passage ? $question->passage->content : '') !!}
            </div>
        </div>
        @elseif($question->passage_highlighted)
        <div class="story-block anim">
            <div class="story-text">
                {!! $question->passage_highlighted !!}
            </div>
        </div>
        @endif

        {{-- Soal --}}
        <div class="question-block anim" id="question-{{ $question->id }}" data-qid="{{ $question->id }}">
            <p class="question-num">Soal {{ $qNum }} dari {{ $totalQuestions }}</p>
            <h3 class="question-text">{!! $question->question_text !!}</h3>

            <div class="options-list" id="opts-{{ $question->id }}">
                @foreach(['A','B','C','D','E'] as $opt)
                    @php $col = 'option_' . strtolower($opt); @endphp
                    @if($question->$col)
                    <button class="option-btn"
                            onclick="selectOption(this, {{ $question->id }}, '{{ $opt }}')"
                            data-q="{{ $question->id }}"
                            data-opt="{{ $opt }}">
                        <span class="option-key">{{ $opt }}</span>
                        <span class="option-label">{{ $question->$col }}</span>
                    </button>
                    @endif
                @endforeach
            </div>

            <div class="feedback-box" id="fb-{{ $question->id }}">
                <p class="feedback-title">—</p>
                <p class="feedback-text"></p>
            </div>
        </div>

    @endforeach

    <div class="nav-row">
        <button class="btn-primary" onclick="submitQuiz()" id="btn-finish" style="opacity:0.4; pointer-events:none;">
            Kumpulkan Jawaban
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 3l5 5-5 5M3 8h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</section>