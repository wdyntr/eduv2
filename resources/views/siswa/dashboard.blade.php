@extends('siswa.layout')
@section('title', 'Dashboard Siswa')

@section('content')

<div style="text-align:center;padding:40px 0 32px;">
    <span class="hero-badge">Selamat Datang</span>
    <h1 class="hero-title" style="font-size:clamp(24px,5vw,42px);margin-bottom:10px;">
        Halo, <em>{{ auth()->user()->name }}</em>
    </h1>
    <p style="font-size:14px;color:var(--text-muted);">
        Berikut informasi ujian yang tersedia untuk Anda.
    </p>
</div>

{{-- ── SESI AKTIF ── --}}
@if($activeSessions->isEmpty())
<div style="
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 60px 32px;
    text-align: center;
    margin-bottom: 28px;
">
    <div style="
        width:60px;height:60px;border-radius:50%;
        background:var(--surface2);border:1px dashed var(--border);
        display:flex;align-items:center;justify-content:center;
        margin:0 auto 20px;font-size:24px;
    ">⏳</div>
    <div style="font-family:var(--ff-display);font-size:20px;color:var(--text);margin-bottom:8px;">
        Belum Ada Ujian Aktif
    </div>
    <p style="font-size:14px;color:var(--text-muted);line-height:1.7;max-width:320px;margin:0 auto;">
        Saat ini tidak ada sesi ujian yang sedang berlangsung.
        Silakan tunggu instruksi dari guru atau admin.
    </p>
</div>

@else
@foreach($activeSessions as $session)
@php $sudahSubmit = in_array($session->id, $submittedIds); @endphp

<div style="
    background: var(--surface);
    border: 1px solid {{ $sudahSubmit ? 'var(--border)' : 'var(--border-accent)' }};
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 20px;
">
    {{-- Header --}}
    <div style="
        background: linear-gradient(135deg, rgba(210,160,80,0.12), rgba(210,160,80,0.04));
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-accent);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    ">
        <div>
            <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;
                        color:var(--gold);margin-bottom:4px;">
                {{ $sudahSubmit ? 'Sudah Dikerjakan' : 'Ujian Berlangsung' }}
            </div>
            <div style="font-family:var(--ff-display);font-size:22px;color:var(--text);">
                {{ $session->paket }}
            </div>
        </div>
        @if($sudahSubmit)
            <span style="font-size:12px;padding:5px 14px;border-radius:100px;
                         background:rgba(74,158,106,0.1);color:var(--success);
                         border:1px solid var(--success);">
                ✓ Selesai
            </span>
        @else
            <span style="font-size:12px;padding:5px 14px;border-radius:100px;
                         background:rgba(210,160,80,0.1);color:var(--gold);
                         border:1px solid var(--border-accent);">
                ● Aktif
            </span>
        @endif
    </div>

    {{-- Detail --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:0;">
        <div style="padding:16px 20px;border-right:1px solid var(--border);">
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;
                        letter-spacing:1px;margin-bottom:4px;">Mata Pelajaran</div>
            <div style="font-weight:500;font-size:13px;line-height:1.5;">
                {{ $sessionSubjects[$session->id] ?? 'Semua Mapel' }}
            </div>
        </div>
        <div style="padding:16px 20px;border-right:1px solid var(--border);">
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;
                        letter-spacing:1px;margin-bottom:4px;">Durasi</div>
            <div style="font-weight:500;font-size:14px;">{{ $session->durasi }} menit</div>
        </div>
        <div style="padding:16px 20px;">
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;
                        letter-spacing:1px;margin-bottom:4px;">Kelas</div>
            <div style="font-weight:500;font-size:14px;">
                {{ $session->kelas ?? 'Semua Kelas' }}
            </div>
        </div>
    </div>

    {{-- CTA --}}
    <div style="padding:16px 24px;border-top:1px solid var(--border);
                display:flex;align-items:center;justify-content:flex-end;">
        @if($sudahSubmit)
            <a href="{{ route('quiz.result', ['session' => $session->id]) }}" class="btn-ghost">
                Lihat Hasil
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor"
                          stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        @else
            <a href="{{ route('quiz.start', ['session' => $session->id]) }}" class="btn-primary">
                Mulai Ujian
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor"
                          stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        @endif
    </div>
</div>
@endforeach
@endif

{{-- ── RIWAYAT UJIAN SELESAI (sesi sudah nonaktif) ── --}}
@if($completedSessions->isNotEmpty())
<div style="margin-top:32px;margin-bottom:12px;">
    <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;
                color:var(--text-dim);margin-bottom:16px;">Riwayat Ujian</div>

    @foreach($completedSessions as $session)
    <div style="
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin-bottom: 14px;
        opacity: 0.9;
    ">
        <div style="
            padding: 16px 20px;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
            border-bottom: 1px solid var(--border);
        ">
            <div>
                <div style="font-size:11px;color:var(--text-dim);letter-spacing:1px;
                            text-transform:uppercase;margin-bottom:3px;">Sudah Selesai</div>
                <div style="font-family:var(--ff-display);font-size:18px;color:var(--text);">
                    {{ $session->paket }}
                </div>
            </div>
            <span style="font-size:12px;padding:4px 12px;border-radius:100px;
                         background:rgba(150,150,150,0.08);color:var(--text-dim);
                         border:1px solid var(--border);">
                Nonaktif
            </span>
        </div>

        <div style="
            padding: 12px 20px;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 8px;
        ">
            <span style="font-size:13px;color:var(--text-muted);">
                {{ $sessionSubjects[$session->id] ?? 'Semua Mapel' }}
                @if($session->ended_at)
                    · Berakhir {{ $session->ended_at->format('d M Y, H:i') }}
                @endif
            </span>
            <a href="{{ route('quiz.result', ['session' => $session->id]) }}" class="btn-ghost"
               style="padding:7px 16px;font-size:13px;">
                Lihat Hasil →
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Info siswa --}}
<div style="
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px 24px;
    margin-top: 20px;
">
    <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;
                color:var(--text-dim);margin-bottom:14px;">Informasi Akun</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:16px;">
        <div>
            <div style="font-size:11px;color:var(--text-dim);margin-bottom:4px;">Nama</div>
            <div style="font-weight:500;">{{ auth()->user()->name }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:var(--text-dim);margin-bottom:4px;">Username</div>
            <div style="font-weight:500;font-family:monospace;">{{ auth()->user()->username }}</div>
        </div>
        @if(auth()->user()->kelas)
        <div>
            <div style="font-size:11px;color:var(--text-dim);margin-bottom:4px;">Kelas</div>
            <div style="font-weight:500;">{{ auth()->user()->kelas }}</div>
        </div>
        @endif
        @if(auth()->user()->no_induk)
        <div>
            <div style="font-size:11px;color:var(--text-dim);margin-bottom:4px;">No. Induk</div>
            <div style="font-weight:500;font-family:monospace;">{{ auth()->user()->no_induk }}</div>
        </div>
        @endif
    </div>
</div>

@if($activeSessions->isNotEmpty() && count(array_diff($activeSessions->pluck('id')->toArray(), $submittedIds)) > 0)
<script>
setInterval(() => {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(() => window.location.reload())
        .catch(() => {});
}, 30000);
</script>
@endif

@endsection
