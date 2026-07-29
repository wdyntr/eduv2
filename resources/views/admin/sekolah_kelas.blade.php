@extends('admin.layouts.base')

@section('title', 'Kelola Kelas Sekolah')
@section('page_title', 'Kelola Kelas & Mata Pelajaran')

@section('content')

<a href="{{ ($session_role ?? 'admin') === 'sekolah' ? '/admin' : '/admin/classroom' }}" class="text-muted small text-decoration-none d-inline-flex align-items-center mb-3">
  <i class="bi bi-arrow-left me-1"></i>Kembali
</a>

<div class="admin-card mb-3">
  <div class="p-3" id="profilSekolahDetailBox">
    <div class="text-center py-3"><div class="spinner-border spinner-border-sm text-success"></div></div>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-collection-play me-2"></i>Link Classroom per Mata Pelajaran</span>
  </div>
  <p class="text-muted small px-3 pt-3 mb-0">
    @if (($session_role ?? 'admin') === 'sekolah')
      Setiap mata pelajaran punya kelas Google Classroom masing-masing. Isi link classroom untuk mata pelajaran
      yang sudah punya kelas — kosongkan kalau belum tersedia. Kolom guru/siswa/task/materi terisi otomatis
      setelah sinkronisasi Google Classroom berjalan.
    @else
      Data ini bersifat <strong>hanya lihat (read-only)</strong> — link Classroom diisi dan dikelola oleh
      operator masing-masing sekolah. Kolom guru/siswa/task/materi terisi otomatis setelah sinkronisasi
      Google Classroom berjalan.
    @endif
  </p>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Mata Pelajaran</th>
          <th>Link Google Classroom</th>
          <th class="text-center">Guru</th>
          <th class="text-center">Siswa</th>
          <th class="text-center">Task Bulan Ini</th>
          <th class="text-center">Materi Bulan Ini</th>
          @if (($session_role ?? 'admin') === 'sekolah')
          <th style="width:110px">Aksi</th>
          @endif
        </tr>
      </thead>
      <tbody id="tabelKelasMapel">
        <tr><td colspan="{{ ($session_role ?? 'admin') === 'sekolah' ? 8 : 7 }}" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>
      </tbody>
    </table>
  </div>
</div>

@endsection

@section('scripts')
<script>
  const SEKOLAH_ID = {{ $sekolah_id }};
  const KELAS_ROLE = "{{ $session_role ?? 'admin' }}";
</script>
<script src="{{ asset('js/admin_sekolah_kelas.js') }}"></script>
@endsection