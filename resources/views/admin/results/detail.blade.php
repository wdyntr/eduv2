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
<div class="breakdown-title" style="margin-bottom:12px;">Jawaban per Soal</div>
<div class="admin-table-wrap">
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
            @forelse($answers as $i => $ans)
            <tr>
                <td style="color:var(--text-dim);font-size:13px;">{{ $i + 1 }}</td>
                <td style="font-size:13px;max-width:320px;line-height:1.5;">
                    {{ Str::limit($ans->question->question_text, 80) }}
                </td>
                <td>
                    <span class="badge {{ $ans->is_correct ? 'badge-success' : 'badge-danger' }}">
                        {{ $ans->answer }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-muted">
                        {{ $ans->question->correct_answer }}
                    </span>
                </td>
                <td>
                    @if($ans->is_correct)
                        <span style="color:var(--success);font-size:13px;">✓ Benar</span>
                    @else
                        <span style="color:var(--danger);font-size:13px;">✗ Salah</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:32px;color:var(--text-muted);">
                    Tidak ada data jawaban.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:20px;">
    <a href="{{ route('admin.results.show', $session) }}" class="btn-ghost">← Kembali</a>
</div>
@endsection