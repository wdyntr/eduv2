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

@if($activeSession)
{{-- Ada sesi aktif --}}
<div style="
    background: var(--surface);
    border: 1px solid var(--border-accent);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 28px;
">
    {{-- Header card --}}
    <div style="
        background: linear-gradient(135deg, rgba(210,160,80,0.12), rgba(210,160,80,0.04));
        padding: 24px 28px;
        border-bottom: 1px solid var(--border-accent);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    ">
        <div>
            <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;
                        color:var(--gold);margin-bottom:6px;">
                Ujian Sedang Berlangsung
            </div>
            <div style="font-family:var(--ff-display);font-size:26px;color:var(--text);">
                {{ $activeSession->paket }}
            </div>
        </div>
        <span class="badge badge-success" style="font-size:12px;padding:5px 14px;">
            ● Aktif
        </span>
    </div>

    {{-- Detail sesi --}}
    <div style="
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0;
    ">
        <div style="padding:20px 24px;border-right:1px solid var(--border);">
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;
                        letter-spacing:1px;margin-bottom:6px;">Mata Pelajaran</div>
            <div style="font-weight:500;font-size:15px;text-transform:capitalize;">
                {{ str_replace('_', ' ', $activeSession->subject) }}
            </div>
        </div>

        <div style="padding:20px 24px;border-right:1px solid var(--border);">
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;
                        letter-spacing:1px;margin-bottom:6px;">Durasi</div>
            {{-- Ganti duration_minutes → durasi --}}
            <div style="font-weight:500;font-size:15px;">
                {{ $activeSession->durasi }} menit
            </div>
        </div>

        <div style="padding:20px 24px;border-right:1px solid var(--border);">
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;
                        letter-spacing:1px;margin-bottom:6px;">Kelas</div>
            <div style="font-weight:500;font-size:15px;">
                {{ $activeSession->kelas ?? 'Semua Kelas' }}
            </div>
        </div>

        @if($activeSession->ended_at)
        <div style="padding:20px 24px;">
            <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;
                        letter-spacing:1px;margin-bottom:6px;">Berakhir Pukul</div>
            <div style="font-weight:500;font-size:15px;color:var(--danger);">
                {{ $activeSession->ended_at->format('H:i') }}
            </div>
        </div>
        @endif
    </div>

    {{-- CTA --}}
    <div style="
        padding: 20px 28px;
        border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    ">
        <p style="font-size:13px;color:var(--text-muted);line-height:1.6;max-width:380px;">
            Pastikan koneksi internet Anda stabil sebelum memulai.
            Jawab semua soal sebelum waktu habis.
        </p>
        <a href="{{ route('quiz.start') }}" class="btn-primary">
            Mulai Ujian
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor"
                      stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>
</div>

@else
{{-- Tidak ada sesi aktif --}}
<div style="
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 60px 32px;
    text-align: center;
    margin-bottom: 28px;
">
    <div style="
        width: 60px; height: 60px; border-radius: 50%;
        background: var(--surface2);
        border: 1px dashed var(--border);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        font-size: 24px;
    ">⏳</div>
    <div style="font-family:var(--ff-display);font-size:20px;color:var(--text);margin-bottom:8px;">
        Belum Ada Ujian Aktif
    </div>
    <p style="font-size:14px;color:var(--text-muted);line-height:1.7;max-width:320px;margin:0 auto;">
        Saat ini tidak ada sesi ujian yang sedang berlangsung.
        Silakan tunggu instruksi dari guru atau admin.
    </p>
</div>
@endif

{{-- Info siswa --}}
<div style="
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px 24px;
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

@endsection