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
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Mulai</th>
                <th>Dinonaktifkan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
            <tr>
                <td><strong>{{ $session->paket }}</strong></td>
                <td style="font-size:12px;color:var(--text-muted);">
                    {{-- Ambil subject unik dari soal paket ini --}}
                    {{ $session->subjectLabel() }}
                </td>
                <td>{{ $session->kelas ?? 'Semua' }}</td>
                <td>{{ $session->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                {{-- Ganti isi kolom ended_at --}}
                <td>
                    @if($session->is_active)
                        <span style="font-size:12px;color:var(--text-muted);">Masih aktif</span>
                    @else
                        {{ $session->ended_at?->format('d/m/Y H:i') ?? '-' }}
                    @endif
                </td>
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

            {{-- di baris empty, ganti colspan 8 → 7 --}}
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                    Belum ada sesi.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $sessions->links('admin.partials.pagination') }}

{{-- Modal buat sesi --}}
<div class="modal-overlay" id="modal-add-session"
     onclick="closeModalOutside(event,'modal-add-session')">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Buat Sesi Ujian</h3>
            <button onclick="closeModal('modal-add-session')" class="drawer-close">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.sessions.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Paket Soal *</label>
                    <select name="paket" id="paket-select" class="admin-select" required
                            onchange="updateSubjectPreview(this)">
                        <option value="">— Pilih Paket —</option>
                        @foreach($pakets as $p)
                            <option value="{{ $p['paket'] }}"
                                    data-subjects="{{ $p['subjects']->implode(', ') }}"
                                    data-total="{{ $p['total'] }}">
                                {{ $p['paket'] }}
                            </option>
                        @endforeach
                    </select>

                    <div id="paket-preview" style="
                        display:none;
                        margin-top:10px;
                        padding:10px 14px;
                        background:var(--surface-2,rgba(255,255,255,.04));
                        border:1px solid var(--border);
                        border-radius:8px;
                        font-size:13px;
                        line-height:1.7;
                    ">
                        <div style="color:var(--text-dim);font-size:11px;letter-spacing:1px;
                                    text-transform:uppercase;margin-bottom:4px;">Info Paket</div>
                        <div>
                            <span style="color:var(--text-muted);">Mata Pelajaran:</span>
                            <strong id="preview-subjects" style="margin-left:6px;"></strong>
                        </div>
                        <div>
                            <span style="color:var(--text-muted);">Total Soal:</span>
                            <strong id="preview-total" style="margin-left:6px;color:var(--gold);"></strong>
                        </div>
                    </div>
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

                {{-- Informasi durasi (read-only) --}}
                <div class="form-group">
                    <label>Durasi</label>
                    <div style="
                        padding:10px 14px;
                        background:var(--surface-2,rgba(255,255,255,.04));
                        border:1px solid var(--border);
                        border-radius:8px;
                        font-size:14px;
                        color:var(--gold);
                        font-weight:500;
                    ">
                        ⏱ 4 jam (240 menit)
                    </div>
                </div>

            </div>

            <div style="display:flex;gap:10px;margin-top:20px;justify-content:flex-end;">
                <button type="button" class="btn-ghost"
                        onclick="closeModal('modal-add-session')">Batal</button>
                <button type="submit" class="btn-primary">Buat Sesi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateSubjectPreview(select) {
    const preview  = document.getElementById('paket-preview');
    const subjects = document.getElementById('preview-subjects');
    const total    = document.getElementById('preview-total');
    const opt      = select.options[select.selectedIndex];

    if (!select.value) {
        preview.style.display = 'none';
        return;
    }

    // Format label mata pelajaran
    const rawSubjects = opt.dataset.subjects || '-';
    const formatted   = rawSubjects
        .split(',')
        .map(s => s.trim().replace(/_/g, ' ')
                    .replace(/\b\w/g, c => c.toUpperCase()))
        .join(', ');

    subjects.textContent = formatted || '-';
    total.textContent    = (opt.dataset.total || '0') + ' soal';
    preview.style.display = 'block';
}
</script>
@endsection
