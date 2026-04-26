<?php
// app/Http/Controllers/QuizController.php
namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $paket = $request->query('paket', 1); // default paket 1

        // Ambil soal beserta passage, dikelompokkan per passage
        $questions = Question::with('passage')
            ->where('paket', $paket)
            ->orderBy('order')
            ->get();

        // Kelompokkan soal berdasarkan passage_id
        $groups = $questions->groupBy('passage_id');

        $totalQuestions = $questions->count();
        $totalPoints    = $questions->sum('points');

        return view('quiz.index', compact('questions', 'groups', 'paket', 'totalQuestions', 'totalPoints'));
    }

    public function check(Request $request)
    {
        $question = Question::findOrFail($request->question_id);
        $isCorrect = strtoupper($request->answer) === $question->correct_answer;

        // Simpan ke session
        $answers = session('quiz_answers', []);
        $answers[$request->question_id] = [
            'correct'       => $isCorrect,
            'question_text' => $question->question_text,
        ];
        session(['quiz_answers' => $answers]);

        return response()->json([
            'correct'     => $isCorrect,
            'feedback'    => $isCorrect
                                ? 'Jawaban kamu benar!'
                                : 'Jawaban kurang tepat. Jawaban yang benar adalah ' . $question->correct_answer . '.',
            'explanation' => $question->subject_matter,
        ]);
    }

    public function result()
    {
        $answers = session('quiz_answers', []);
        $correct = collect($answers)->where('correct', true)->count();
        $total   = count($answers);

        return response()->json([
            'correct'  => $correct,
            'total'    => $total,
            'answers'  => $answers,
        ]);
    }
}