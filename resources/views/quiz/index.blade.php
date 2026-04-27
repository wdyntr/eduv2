{{-- resources/views/quiz/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quiz — Paket {{ $paket }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

@include('quiz.partials._start')
@include('quiz.partials._progress')

<div id="quiz-wrapper" style="display:none;">
    @include('quiz.partials._question-nav')
    @include('quiz.partials._questions')
</div>

@include('quiz.partials._result')

<script>
    const TOTAL      = {{ $totalQuestions }};
    const SUBMIT_URL = '{{ route("quiz.submit") }}';
    const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
</script>
<script src="{{ asset('js/quiz.js') }}"></script>
</body>
</html>
