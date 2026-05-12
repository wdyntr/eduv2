@extends('admin.layout')
@section('title', 'Detail — ' . $user->name)

@section('content')

{{-- Header info --}}
<div class="result-breakdown" style="margin-bottom:24px;">
    <div class="breakdown-title">Informasi Siswa</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:16px;margin-top:12px;">
        <div>
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;letter-spacing:1px;">Nama</div>
            <div style="font-weight:500;margin-top:4px;">{{ $user->name }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;letter-spacing:1px;">Kelas</div>
            <div style="font-weight:500;margin-top:4px;">{{ $user->kelas ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;letter-spacing:1px;">Paket</div>
            <div style="font-weight:500;margin-top:4px;">{{ $session->paket }}</div>
        </div>
        @if($result)
        <div>
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;letter-spacing:1px;">Skor</div>
            <div style="font-weight:500;color:var(--gold);font-size:20px;margin-top:4px;">{{ $result->score }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;letter-spacing:1px;">Benar</div>
            <div style="font-weight:500;color:var(--success);margin-top:4px;">
                {{ $result->correct_count }} / {{ $result->total_questions }}
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Detail jawaban --}}
@php
    $grouped = $questions->groupBy(fn($q) => $q->passage?->subject ?? 'lainnya');
@endphp

@foreach($grouped as $subject => $groupQuestions)
@php
    $benar = $groupQuestions->filter(fn($q) =>
        isset($answers[$q->id]) && $answers[$q->id]->is_correct
    )->count();
@endphp
<div class="breakdown-title" style="margin:20px 0 10px;">
    {{ ucwords(str_replace('_', ' ', $subject)) }}
    <span style="color:var(--text-muted);font-weight:400;font-size:12px;">
        ({{ $benar }}/{{ $groupQuestions->count() }} benar)
    </span>
</div>
<div class="admin-table-wrap" style="margin-bottom:20px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Pertanyaan</th>
                <th>Jawaban Siswa</th>
                <th>Jawaban Benar</th>
                <th>Hasil</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupQuestions as $i => $question)
            @php $ans = $answers[$question->id] ?? null; @endphp
            <tr>
                <td style="color:var(--text-dim);font-size:13px;">{{ $i + 1 }}</td>
                <td style="font-size:13px;max-width:320px;line-height:1.6;" class="katex-cell">
                    {!! $question->question_text !!}
                </td>
                <td>
                    @if($ans)
                        <span class="badge {{ $ans->is_correct ? 'badge-success' : 'badge-danger' }}">
                            {{ $ans->answer }}
                        </span>
                    @else
                        <span class="badge badge-muted">—</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-muted">{{ $question->correct_answer }}</span>
                </td>
                <td>
                    @if(!$ans)
                        <span style="color:var(--text-dim);font-size:13px;">— Tidak dijawab</span>
                    @elseif($ans->is_correct)
                        <span style="color:var(--success);font-size:13px;">✓ Benar</span>
                    @else
                        <span style="color:var(--danger);font-size:13px;">✗ Salah</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach {{-- ← hapus duplikat @endforeach yang sebelumnya ada --}}

<div style="margin-top:20px;">
    <a href="{{ route('admin.results.show', $session) }}" class="btn-ghost">← Kembali</a>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    renderMathInElement(document.body, {
        delimiters: [
            { left: '$$', right: '$$', display: true  },
            { left: '$',  right: '$',  display: false },
        ],
        throwOnError: false,
        output: 'html',
    });
});
</script>
<style>
.katex-cell .katex        { font-size: 0.92em !important; }
.katex-cell .katex-display { margin: 6px 0; overflow-x: auto; }
</style>
@endsection
