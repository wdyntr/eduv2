{{-- resources/views/quiz/partials/_questions.blade.php --}}
<section id="screen-quiz" class="screen-quiz">

    @php
        $qNum           = 0;
        $currentSubject = null;

        $subjectLabels = [
            'bahasa_indonesia' => 'Bahasa Indonesia',
            'bahasa_inggris'   => 'Bahasa Inggris',
            'matematika'       => 'Matematika',
        ];

        $subjectIcons = [
            'bahasa_indonesia' => '📝',
            'bahasa_inggris'   => '🌐',
            'matematika'       => '🔢',
        ];
    @endphp

    @foreach($questions as $question)
        @php
            $qNum++;

            $passageContent = $question->passage_highlighted
                ?? ($question->passage ? $question->passage->content : null);

            $prevQuestion = $loop->first ? null : $questions[$loop->index - 1];
            $isNewPassage = $loop->first
                || $question->passage_id !== $prevQuestion->passage_id
                || $question->passage_highlighted !== $prevQuestion->passage_highlighted;

            $passageText = trim(strip_tags($passageContent ?? ''));
            $hasPassage  = !empty($passageText) && strtolower($passageText) !== 'nan';

            // Deteksi pergantian subject
            $thisSubject    = $question->passage?->subject ?? null;
            $isNewSubject   = $thisSubject !== $currentSubject;
            $currentSubject = $thisSubject;
        @endphp

        {{-- ── HEADER MATA PELAJARAN — tampil saat subject berganti ── --}}
        @if($isNewSubject && $thisSubject)
        <div class="subject-divider anim">
            <div class="subject-divider-inner">
                <span class="subject-divider-icon">
                    {{ $subjectIcons[$thisSubject] ?? '📚' }}
                </span>
                <div>
                    <div class="subject-divider-label">Mata Pelajaran</div>
                    <div class="subject-divider-name">
                        {{ $subjectLabels[$thisSubject] ?? ucwords(str_replace('_', ' ', $thisSubject)) }}
                    </div>
                </div>
            </div>
            <div class="subject-divider-line"></div>
        </div>
        @endif

        {{-- Teks bacaan --}}
        @if($isNewPassage && $hasPassage)
        <div class="story-block anim">
            <div class="story-text">
                {!! $passageContent !!}
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
                        <span class="option-label">{!! $question->$col !!}</span>
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
        <button class="btn-primary" onclick="submitQuiz()" id="btn-finish"
                style="opacity:0.4; pointer-events:none;">
            Kumpulkan Jawaban
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 3l5 5-5 5M3 8h10" stroke="currentColor"
                      stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</section>
