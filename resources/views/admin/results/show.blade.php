@extends('admin.layout')
@section('title', 'Hasil — ' . $session->paket)

@section('content')

{{-- Info sesi --}}
<div class="result-breakdown" style="margin-bottom:24px;">
    <div class="breakdown-title">Informasi Sesi</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:16px;margin-top:12px;">
        <div>
            <div style="font-size:11px;color:var(--text-dim);letter-spacing:1px;text-transform:uppercase;">Paket</div>
            <div style="font-weight:500;margin-top:4px;">{{ $session->paket }}</div>
        </div>
        {{-- Ganti baris Mapel --}}
        <div>
            <div style="font-size:11px;color:var(--text-dim);letter-spacing:1px;text-transform:uppercase;">Mapel</div>
            <div style="font-weight:500;margin-top:4px;">{{ $subjectLabel }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:var(--text-dim);letter-spacing:1px;text-transform:uppercase;">Kelas</div>
            <div style="font-weight:500;margin-top:4px;">{{ $session->kelas ?? 'Semua' }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:var(--text-dim);letter-spacing:1px;text-transform:uppercase;">Durasi</div>
            <div style="font-weight:500;margin-top:4px;">{{ $session->durasi }} menit</div>
        </div>
        <div>
            <div style="font-size:11px;color:var(--text-dim);letter-spacing:1px;text-transform:uppercase;">Peserta Submit</div>
            <div style="font-weight:500;color:var(--gold);margin-top:4px;">{{ $results->count() }} siswa</div>
        </div>
    </div>
</div>

{{-- Siswa yang sudah submit --}}
<div class="breakdown-title" style="margin-bottom:12px;">Hasil Pengerjaan</div>
<div class="admin-table-wrap" style="margin-bottom:28px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Benar</th>
                <th>Total Soal</th>
                <th>Skor</th>
                <th>Waktu Submit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $i => $result)
            <tr>
                <td style="color:var(--text-dim);font-size:13px;">{{ $i + 1 }}</td>
                <td class="td-name">{{ $result->user->name }}</td>
                <td>{{ $result->user->kelas ?? '-' }}</td>
                <td style="color:var(--success);font-weight:500;">{{ $result->correct_count }}</td>
                <td style="color:var(--text-muted);">{{ $totalQuestions }}</td>
                <td>
                    <span style="
                        color: {{ $result->score >= 75 ? 'var(--success)' : ($result->score >= 50 ? 'var(--gold)' : 'var(--danger)') }};
                        font-weight: 500;
                        font-size: 15px;
                    ">{{ $result->score }}</span>
                </td>
                <td style="font-size:12px;color:var(--text-muted);">
                    {{ $result->submitted_at?->format('d/m H:i') ?? '-' }}
                </td>
                <td>
                    <a href="{{ route('admin.results.detail', [$session, $result->user]) }}"
                       class="btn-icon">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:32px;color:var(--text-muted);">
                    Belum ada siswa yang submit.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Rekap per Mata Pelajaran --}}
@if($results->count() > 0)
<div class="breakdown-title" style="margin:24px 0 12px;">Rata-rata per Mata Pelajaran</div>
<div class="admin-table-wrap" style="margin-bottom:28px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Mata Pelajaran</th>
                <th>Rata-rata Benar</th>
                <th>Rata-rata Skor</th>
                <th>Peserta</th>
            </tr>
        </thead>
        <tbody>
            @php
                $userIds   = $results->pluck('user_id');
                $allAnswers = \App\Models\SiswaAnswer::where('session_id', $session->id)
                    ->whereIn('user_id', $userIds)
                    ->with('question.passage')
                    ->get();

                $bySubject = $allAnswers
                    ->filter(fn($a) => $a->question?->passage)
                    ->groupBy(fn($a) => $a->question->passage->subject);
            @endphp

            @foreach($bySubject as $subject => $answers)
            @php
                $totalSiswa  = $answers->pluck('user_id')->unique()->count();
                $avgBenar    = $answers->where('is_correct', true)->count() / max($totalSiswa, 1);
                $avgSkor     = $answers->where('is_correct', true)
                                       ->sum(fn($a) => $a->question->points ?? 1) / max($totalSiswa, 1);
            @endphp
            <tr>
                <td>{{ ucwords(str_replace('_', ' ', $subject)) }}</td>
                <td style="color:var(--success);">{{ number_format($avgBenar, 1) }} soal</td>
                <td style="color:var(--gold);font-weight:500;">{{ number_format($avgSkor, 1) }}</td>
                <td style="color:var(--text-muted);">{{ $totalSiswa }} siswa</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Siswa yang belum submit --}}
@if($notSubmitted->count() > 0)
<div class="breakdown-title" style="margin-bottom:12px;">
    Belum Submit
    <span style="color:var(--danger);font-weight:400;font-size:12px;">
        ({{ $notSubmitted->count() }} siswa)
    </span>
</div>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr><th>Nama</th><th>Kelas</th><th>No. Induk</th></tr>
        </thead>
        <tbody>
            @foreach($notSubmitted as $s)
            <tr>
                <td class="td-name">{{ $s->name }}</td>
                <td>{{ $s->kelas ?? '-' }}</td>
                <td class="td-mono">{{ $s->no_induk ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div style="margin-top:20px;">
    <a href="{{ route('admin.results.index') }}" class="btn-ghost">← Kembali</a>
</div>
@endsection
