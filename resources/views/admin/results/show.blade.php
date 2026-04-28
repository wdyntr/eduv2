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
        <div>
            <div style="font-size:11px;color:var(--text-dim);letter-spacing:1px;text-transform:uppercase;">Mapel</div>
            <div style="font-weight:500;margin-top:4px;">{{ str_replace('_',' ',$session->subject) }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:var(--text-dim);letter-spacing:1px;text-transform:uppercase;">Kelas</div>
            <div style="font-weight:500;margin-top:4px;">{{ $session->kelas ?? 'Semua' }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:var(--text-dim);letter-spacing:1px;text-transform:uppercase;">Durasi</div>
            <div style="font-weight:500;margin-top:4px;">{{ $session->duration_minutes }} menit</div>
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
                <td style="color:var(--text-muted);">{{ $result->total_questions }}</td>
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