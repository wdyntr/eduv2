// public/js/admin.js

function openModal(id) {
  document.getElementById(id).classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal(id) {
  document.getElementById(id).classList.remove('open');
  document.body.style.overflow = '';
}

function closeModalOutside(event, id) {
  if (event.target.id === id) closeModal(id);
}

function openEdit(userId) {
  document.querySelectorAll('.edit-row').forEach(r => r.style.display = 'none');
  document.getElementById('edit-row-' + userId).style.display = 'table-row';
}

function closeEdit(userId) {
  document.getElementById('edit-row-' + userId).style.display = 'none';
}

function toggleSiswaFields(role) {
  document.querySelectorAll('.siswa-only').forEach(el => {
    el.style.display = role === 'siswa' ? 'flex' : 'none';
  });
}

function toggleSidebar() {
    const sidebar  = document.querySelector('.admin-sidebar');
    const overlay  = document.getElementById('admin-sidebar-overlay');
    const isOpen   = sidebar.classList.contains('open');

    sidebar.classList.toggle('open', !isOpen);
    overlay.classList.toggle('show', !isOpen);
    document.body.style.overflow = isOpen ? '' : 'hidden';
}

window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        const sidebar = document.querySelector('.admin-sidebar');
        const overlay = document.getElementById('admin-sidebar-overlay');
        sidebar?.classList.remove('open');
        overlay?.classList.remove('show');
        document.body.style.overflow = '';
    }
});

// ── SESSION KEEPALIVE ──────────────────────────────────────
// Ping setiap 10 menit agar session & CSRF token tidak expire
// saat admin membiarkan halaman terbuka lama
setInterval(() => {
    fetch(window.location.href, {
        method: 'HEAD',
        credentials: 'same-origin',
    }).catch(() => {});
}, 10 * 60 * 1000);

// ── SAFE LOGOUT — ambil CSRF token terbaru sebelum logout ──
async function safeLogout() {
    try {
        const res  = await fetch('/logout-token');
        const data = await res.json();
        const form = document.getElementById('logout-form');
        if (form) {
            form.querySelector('input[name="_token"]').value = data.token;
            form.submit();
        }
    } catch (e) {
        // Fallback jika gagal ambil token
        window.location.href = '/logout';
    }
}

// Auto-refresh halaman sesi setiap 60 detik
// Hanya aktif di halaman sessions
if (window.location.pathname.includes('/admin/sessions')) {
    setInterval(() => {
        window.location.reload();
    }, 60 * 1000);
}
