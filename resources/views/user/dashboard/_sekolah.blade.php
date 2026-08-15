<!-- SEKOLAH -->
<div class="mb-4">
  <h5 class="fw-bold mb-1" style="font-family:'Sora',sans-serif">Halo 👋</h5>
  <p class="text-muted mb-0">Berikut ringkasan aktivitas classroom sekolah kamu bulan ini.</p>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-collection-play" style="color:#2d5090"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="dashStatKelasTerisi">-</div>
        <div class="stat-label">Kelas Terisi</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-people text-success"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="dashStatSiswa">-</div>
        <div class="stat-label">Total Siswa</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon gold"><i class="bi bi-clipboard-check" style="color:#c47a00"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="dashStatTask">-</div>
        <div class="stat-label">Task Diberikan</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon red"><i class="bi bi-cloud-upload" style="color:#dc3545"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="dashStatMateri">-</div>
        <div class="stat-label">Materi Diunggah</div>
      </div>
    </div>
  </div>
</div>

<div class="admin-card">
  <div class="p-4 text-center">
    <i class="bi bi-collection-play" style="font-size:2rem;color:#2d5090"></i>
    <p class="mb-3 mt-2 text-muted">Kelola link Classroom & lihat detail statistik per mata pelajaran.</p>
    <a href="/admin/classroom/{{ $session_sekolah_id }}" class="btn btn-admin-primary btn-sm">
      <i class="bi bi-arrow-right me-1"></i>Kelola Kelas Mata Pelajaran
    </a>
  </div>
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', async () => {
  const sekolahId = {{ $session_sekolah_id ?? 'null' }};
  if (!sekolahId) return;

  try {
    const res  = await fetch(`/api/sekolah/${sekolahId}/kelas`);
    const data = await res.json();
    if (!res.ok) return;

    const kelas = data.kelas || [];
    const terisi = kelas.filter(k => k.classroom_url).length;
    const totalSiswa  = kelas.reduce((sum, k) => sum + (k.jumlah_siswa || 0), 0);
    const totalTask   = kelas.reduce((sum, k) => sum + (k.jumlah_task || 0), 0);
    const totalMateri = kelas.reduce((sum, k) => sum + (k.jumlah_materi || 0), 0);

    document.getElementById('dashStatKelasTerisi').textContent = `${terisi} / ${kelas.length}`;
    document.getElementById('dashStatSiswa').textContent = totalSiswa;
    document.getElementById('dashStatTask').textContent = totalTask;
    document.getElementById('dashStatMateri').textContent = totalMateri;
  } catch {}
});
</script>
@endonce