@extends('admin.layout')
@section('title', 'Hasil Ujian')

@section('content')
<div class="page-header">
    <h2 class="page-subtitle">Rekap hasil ujian per sesi</h2>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Paket</th>
                <th>Mapel</th>
                <th>Kelas</th>
                <th>Durasi</th>
                <th>Tanggal</th>
                <th>Peserta</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
            <tr>
                <td><strong>{{ $session->paket }}</strong></td>
                <td>{{ str_replace('_', ' ', $session->subject) }}</td>
                <td>{{ $session->kelas ?? 'Semua' }}</td>
                <td>{{ $session->durasi }} menit</td>
                <td style="font-size:13px;color:var(--text-muted);">
                    {{ $session->started_at?->format('d/m/Y') ?? '-' }}
                </td>
                <td>
                    <span style="color:var(--gold);font-weight:500;">{{ $session->hasil_count }}</span>
                    <span style="color:var(--text-dim);font-size:12px;"> siswa</span>
                </td>
                <td>
                    <span class="badge {{ $session->is_active ? 'badge-success' : 'badge-muted' }}">
                        {{ $session->is_active ? 'Aktif' : 'Selesai' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.results.show', $session) }}" class="btn-icon">
                        Detail →
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                    Belum ada sesi ujian.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $sessions->links('admin.partials.pagination') }}
@endsection