{{-- resources/views/admin/sessions/index.blade.php --}}
@extends('admin.layout')
@section('title', 'Sesi Ujian')

@section('content')
<div class="page-header">
    <h2 class="page-subtitle">Aktifkan paket soal untuk ujian siswa</h2>
    <button class="btn-primary" onclick="openModal('modal-add-session')">
        + Buat Sesi
    </button>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Paket</th>
                <th>Mapel</th>
                <th>Kelas</th>
                <th>Durasi</th>
                <th>Mulai</th>
                <th>Selesai</th>
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
                <td>{{ $session->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                <td>{{ $session->ended_at?->format('d/m/Y H:i') ?? '-' }}</td>
                <td>
                    <span class="badge {{ $session->is_active ? 'badge-success' : 'badge-muted' }}">
                        {{ $session->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="td-actions">
                    <form method="POST"
                          action="{{ route('admin.sessions.toggle', $session) }}"
                          style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="btn-icon {{ $session->is_active ? 'btn-icon-danger' : '' }}">
                            {{ $session->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <a href="{{ route('admin.results.show', $session) }}" class="btn-icon">
                        Lihat Hasil
                    </a>
                    @if(!$session->is_active)
                    <form method="POST"
                          action="{{ route('admin.sessions.destroy', $session) }}"
                          onsubmit="return confirm('Hapus sesi ini?')"
                          style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon btn-icon-danger">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">Belum ada sesi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $sessions->links('admin.partials.pagination') }}

<div class="modal-overlay" id="modal-add-session" onclick="closeModalOutside(event,'modal-add-session')">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Buat Sesi Ujian</h3>
            <button onclick="closeModal('modal-add-session')" class="drawer-close">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.sessions.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Paket Soal *</label>
                    <select name="paket" class="admin-select" required>
                        @foreach($pakets as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Mata Pelajaran *</label>
                    <select name="subject" class="admin-select" required>
                        <option value="matematika">Matematika</option>
                        <option value="bahasa_inggris">Bahasa Inggris</option>
                        <option value="bahasa_indonesia">Bahasa Indonesia</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kelas <small>(kosongkan = semua kelas)</small></label>
                    <select name="kelas" class="admin-select">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Durasi (menit) *</label>
                    <input type="number" name="durasi" value="90" min="5" max="300" class="admin-input" required>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;justify-content:flex-end;">
                <button type="button" class="btn-ghost" onclick="closeModal('modal-add-session')">Batal</button>
                <button type="submit" class="btn-primary">Buat Sesi</button>
            </div>
        </form>
    </div>
</div>
@endsection