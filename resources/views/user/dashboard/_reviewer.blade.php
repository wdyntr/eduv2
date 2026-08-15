<!-- REVIEWER: STAT CARDS -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-4">
    <div class="stat-card">
      <div class="stat-icon gold"><i class="bi bi-hourglass-split" style="color:#c47a00"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statJurnalPending">-</div>
        <div class="stat-label">Menunggu Review</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-4">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-check-circle text-success"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statJurnalApproved">-</div>
        <div class="stat-label">Disetujui</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-4">
    <div class="stat-card">
      <div class="stat-icon red"><i class="bi bi-x-circle" style="color:#dc3545"></i></div>
      <div class="stat-info">
        <div class="stat-num" id="statJurnalRejected">-</div>
        <div class="stat-label">Ditolak</div>
      </div>
    </div>
  </div>
</div>

<!-- REVIEWER: ANTRIAN REVIEW -->
<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="bi bi-journal-check me-2"></i>Jurnal Menunggu Review</span>
    <a href="/admin/jurnal" class="btn-admin-primary btn">
      <i class="bi bi-arrow-right me-1"></i>Lihat Semua
    </a>
  </div>
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Judul</th>
          <th>Kategori</th>
          <th>Pengaju</th>
          <th>Versi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tabelJurnalReview">
        <tr>
          <td colspan="5" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-success"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

@once
<script>
  async function loadDashboardReviewer() {
    try {
      const res = await fetch('/api/jurnal/pending');
      const data = await res.json();
      const items = data.items || [];

      document.getElementById('statJurnalPending').textContent = items.length;

      const [approvedRes, rejectedRes] = await Promise.all([
        fetch('/api/jurnal/all?status=approved'),
        fetch('/api/jurnal/all?status=rejected'),
      ]);
      const approved = (await approvedRes.json()).items || [];
      const rejected = (await rejectedRes.json()).items || [];
      document.getElementById('statJurnalApproved').textContent = approved.length;
      document.getElementById('statJurnalRejected').textContent = rejected.length;

      const tbody = document.getElementById('tabelJurnalReview');
      if (!items.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada jurnal menunggu review.</td></tr>';
        return;
      }
      tbody.innerHTML = items.slice(0, 5).map(j => `
        <tr>
          <td>${j.judul ?? '-'}</td>
          <td>${j.kategori ?? '-'}</td>
          <td>${j.nama_pengaju ?? '-'}</td>
          <td>v${j.versi_ke ?? 1}</td>
          <td><a href="/admin/jurnal" class="btn btn-sm btn-outline-primary">Review</a></td>
        </tr>
      `).join('');
    } catch (e) {
      document.getElementById('tabelJurnalReview').innerHTML =
        '<tr><td colspan="5" class="text-center py-4 text-danger">Gagal memuat data.</td></tr>';
    }
  }
</script>
@endonce