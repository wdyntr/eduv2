<!-- OPERATOR: STAT CARDS -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-4">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-collection-play text-success"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statMateriOperator">-</div>
        <div class="stat-label">Total Materi</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-4">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-camera-video" style="color:#2d5090"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statClassroomTerisi">-</div>
        <div class="stat-label">Kelas Classroom Terisi</div>
      </div>
    </div>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-clock-history me-2"></i>Materi Terbaru</span>
    <a href="/admin/materi/tambah" class="btn-admin-primary btn">
      <i class="bi bi-plus-lg me-1"></i>Tambah Materi
    </a>
  </div>
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Judul</th>
          <th>Jenjang</th>
          <th>Tipe</th>
          <th>Mata Pelajaran</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelMateriOperator">
        <tr>
          <td colspan="6" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-success"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

@once
<script>
  async function loadDashboardOperator() {
    try {
      const res = await fetch('/api/materi?limit=5&sort=terbaru');
      const data = await res.json();
      document.getElementById('statMateriOperator').textContent = data.total ?? (data.items || []).length;

      const tbody = document.getElementById('tabelMateriOperator');
      const items = data.items || [];
      tbody.innerHTML = items.length ? items.map(m => `
        <tr>
          <td>${m.judul ?? '-'}</td>
          <td>${(m.jenjang || '-').toUpperCase()}</td>
          <td>${m.tipe ?? '-'}</td>
          <td>${m.mata_pelajaran ?? '-'}</td>
          <td>${m.created_at ? new Date(m.created_at).toLocaleDateString('id-ID') : '-'}</td>
          <td><a href="/admin/materi/${m.id}/edit" class="btn btn-sm btn-outline-primary">Edit</a></td>
        </tr>
      `).join('') : '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada materi.</td></tr>';
    } catch (e) {
      document.getElementById('tabelMateriOperator').innerHTML =
        '<tr><td colspan="6" class="text-center py-4 text-danger">Gagal memuat data.</td></tr>';
    }

    try {
      const res = await fetch('/api/classroom');
      const data = await res.json();
      const total = (data.items || []).reduce((sum, s) => sum + (s.kelas_terisi || 0), 0);
      document.getElementById('statClassroomTerisi').textContent = total;
    } catch (e) {}
  }
</script>
@endonce