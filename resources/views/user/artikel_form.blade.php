@extends('user.layouts.base')

@section('title', $artikel ? 'Edit Artikel' : 'Tambah Artikel')
@section('page_title', $artikel ? 'Edit Artikel' : 'Tambah Artikel')

@section('content')

<a href="/admin/artikel" class="d-inline-flex align-items-center gap-1 text-decoration-none mb-3 small text-muted">
  <i class="bi bi-arrow-left"></i> Kembali ke Daftar Artikel
</a>

<div class="admin-card p-4">
  <div id="formAlert" class="alert d-none mb-3"></div>

  <div class="mb-3">
    <label class="form-label small fw-semibold">Judul Artikel <span class="text-danger">*</span></label>
    <input type="text" id="fJudul" class="form-control" value="{{ $artikel->judul ?? '' }}" placeholder="Judul artikel">
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <label class="form-label small fw-semibold">Kategori <span class="text-danger">*</span></label>
      <select id="fKategori" class="form-select">
        <option value="">Memuat kategori...</option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label small fw-semibold">Tipe Konten <span class="text-danger">*</span></label>
      <select id="fTipe" class="form-select" onchange="toggleVideoUrl()">
        <option value="artikel" {{ ($artikel->tipe ?? '') === 'artikel' ? 'selected' : '' }}>📰 Artikel</option>
        <option value="video" {{ ($artikel->tipe ?? '') === 'video' ? 'selected' : '' }}>🎬 Video</option>
      </select>
    </div>
  </div>

  <div class="mb-3 d-none" id="wrapVideoUrl">
    <label class="form-label small fw-semibold">URL Video YouTube <span class="text-danger">*</span></label>
    <input type="text" id="fVideoUrl" class="form-control" value="{{ $artikel->video_url ?? '' }}" placeholder="https://youtube.com/watch?v=...">
  </div>

  <div class="mb-3">
    <label class="form-label small fw-semibold">Thumbnail</label>
    <input type="text" id="fThumbnail" class="form-control" value="{{ $artikel->thumbnail ?? '' }}" placeholder="https://..." readonly>
    <div class="form-text" id="thumbnailHint">Thumbnail otomatis terisi dari video YouTube jika tipe konten adalah Video.</div>
</div>

  <div class="mb-3">
    <label class="form-label small fw-semibold">Konten <span class="text-danger">*</span></label>
    <textarea id="fKonten" class="form-control" rows="12" placeholder="Isi artikel...">{{ $artikel->konten ?? '' }}</textarea>
  </div>

  <div class="mb-3 form-check form-switch">
    <input type="checkbox" id="fIsActive" class="form-check-input" {{ ($artikel->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label small" for="fIsActive">Tampilkan di website (aktif)</label>
  </div>

  <div class="d-flex gap-2">
    <button class="btn-admin-primary btn" onclick="submitArtikel({{ $artikel->id ?? 'null' }})">
      <i class="bi bi-check-lg me-1"></i>Simpan
    </button>
    <a href="/admin/artikel" class="btn btn-outline-secondary">Batal</a>
  </div>
</div>

@endsection

@section('scripts')
<script>
  const ARTIKEL_EDIT_KATEGORI_ID = @json($artikel->kategori_id ?? null);
</script>
<script src="{{ asset('js/admin_artikel.js') }}"></script>
@endsection