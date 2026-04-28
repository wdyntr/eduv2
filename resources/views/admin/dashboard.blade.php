@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Siswa</div>
        <div class="stat-value">{{ $stats['total_siswa'] }}</div>
        <div class="stat-sub">Pengguna terdaftar</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Admin</div>
        <div class="stat-value">{{ $stats['total_admin'] }}</div>
        <div class="stat-sub">Pengelola sistem</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Sesi Aktif</div>
        <div class="stat-value">{{ $stats['sesi_aktif'] }}</div>
        <div class="stat-sub">Ujian sedang berjalan</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Hasil</div>
        <div class="stat-value">{{ $stats['total_hasil'] }}</div>
        <div class="stat-sub">Pengumpulan jawaban</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:8px;">

    {{-- Sesi Terbaru --}}
    <div>
        <div class="breakdown-title" style="margin-bottom:12px;">Sesi Ujian Terbaru</div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Paket</th>
                        <th>Mapel</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSessions as $s)
                    <tr>
                        <td><strong>{{ $s->paket }}</strong></td>
                        <td style="font-size:12px;color:var(--text-muted);">
                            {{ str_replace('_', ' ', $s->subject) }}
                        </td>
                        <td>
                            <span class="badge {{ $s->is_active ? 'badge-success' : 'badge-muted' }}">
                                {{ $s->is_active ? 'Aktif' : 'Selesai' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--text-muted);">
                        Belum ada sesi.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:10px;">
            <a href="{{ route('admin.sessions.index') }}" class="btn-ghost" style="font-size:13px;">
                Lihat semua sesi →
            </a>
        </div>
    </div>

    {{-- Hasil Terbaru --}}
    <div>
        <div class="breakdown-title" style="margin-bottom:12px;">Hasil Ujian Terbaru</div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Paket</th>
                        <th>Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentResults as $r)
                    <tr>
                        <td class="td-name" style="font-size:13px;">{{ $r->user->name }}</td>
                        <td style="font-size:12px;color:var(--text-muted);">
                            {{ $r->session->paket ?? '-' }}
                        </td>
                        <td>
                            <span style="color:var(--gold);font-weight:500;">
                                {{ $r->score }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--text-muted);">
                        Belum ada hasil.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:10px;">
            <a href="{{ route('admin.results.index') }}" class="btn-ghost" style="font-size:13px;">
                Lihat semua hasil →
            </a>
        </div>
    </div>

</div>
@endsection