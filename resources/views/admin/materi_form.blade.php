@extends('admin.layouts.base')

@section('title', isset($materi) ? 'Edit Materi' : 'Tambah Materi')
@section('page_title', isset($materi) ? 'Edit Materi' : 'Tambah Materi')

@section('content')

<div class="admin-form-card">
  <div class="mb-4">
    <a href="/admin/materi" class="text-muted small">
      <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Materi
    </a>
  </div>

  <div id="formAlert" class="alert d-none mb-4"></div>

  <div class="row g-3">
    <div class="col-12">
      <label class="form-label">Judul Materi <span class="text-danger">*</span></label>
      <input type="text" id="fJudul" class="form-control"
        placeholder="Contoh: Limit dan Turunan Fungsi"
        value="{{ $materi->judul ?? '' }}">
    </div>

    <div class="col-md-6">
      <label class="form-label">Jenjang <span class="text-danger">*</span></label>
      <select id="fJenjang" class="form-select" onchange="loadMapelOptions()">
        <option value="">Pilih Jenjang</option>
        <option value="sma" {{ isset($materi) && $materi->jenjang == 'sma' ? 'selected' : '' }}>SMA</option>
        <option value="smk" {{ isset($materi) && $materi->jenjang == 'smk' ? 'selected' : '' }}>SMK</option>
        <option value="slb" {{ isset($materi) && $materi->jenjang == 'slb' ? 'selected' : '' }}>SLB</option>
      </select>
    </div>

    <div class="col-md-6">
      <label class="form-label">Tipe Konten <span class="text-danger">*</span></label>
      <select id="fTipe" class="form-select">
        <option value="">Pilih Tipe</option>
        <option value="video" {{ isset($materi) && $materi->tipe == 'video' ? 'selected' : '' }}>🎬 Video YouTube</option>
        <option value="ppt"   {{ isset($materi) && $materi->tipe == 'ppt'   ? 'selected' : '' }}>📑 Slide PPT</option>
        <option value="pdf"   {{ isset($materi) && $materi->tipe == 'pdf'   ? 'selected' : '' }}>📄 Modul PDF</option>
      </select>
    </div>

    <div class="col-12">
      <label class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
      <input type="text" id="fMapelInput" class="form-control"
        placeholder="Pilih jenjang dulu, lalu ketik mata pelajaran..."
        list="mapelList"
        autocomplete="off"
        value="{{ $materi->mapel->nama ?? '' }}"
        {{ isset($materi) ? '' : 'disabled' }}>
      <datalist id="mapelList"></datalist>
      <input type="hidden" id="fMapel">
      <div class="form-text" id="mapelHint">Pilih jenjang terlebih dahulu.</div>
    </div>

    <div class="col-12">
      <label class="form-label">URL Konten <span class="text-danger">*</span></label>
      <input type="url" id="fUrl" class="form-control"
        placeholder="https://youtube.com/watch?v=... atau https://drive.google.com/..."
        value="{{ $materi->url ?? '' }}">
      <div class="form-text">YouTube URL untuk video, Google Drive URL untuk PPT/PDF.</div>
    </div>

    <div class="col-12">
      <label class="form-label">Deskripsi</label>
      <textarea id="fDeskripsi" class="form-control" rows="3"
        placeholder="Deskripsi singkat materi...">{{ $materi->deskripsi ?? '' }}</textarea>
    </div>

    <div class="col-12 mt-2 d-flex gap-2">
      <button class="btn btn-admin-primary" onclick="submitMateri({{ $materi->id ?? 'null' }})">
        <i class="bi bi-check-lg me-1"></i>
        {{ isset($materi) ? 'Simpan Perubahan' : 'Tambah Materi' }}
      </button>
      <a href="/admin/materi" class="btn btn-outline-secondary" style="border-radius:10px">Batal</a>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    @if (isset($materi))
      loadMapelOptions('{{ $materi->mapel_id }}', null);
    @endif
  });
</script>
@endsection