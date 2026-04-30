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
  // Tutup semua edit row lain
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

// Tutup sidebar saat resize ke desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        const sidebar = document.querySelector('.admin-sidebar');
        const overlay = document.getElementById('admin-sidebar-overlay');
        sidebar?.classList.remove('open');
        overlay?.classList.remove('show');
        document.body.style.overflow = '';
    }
});
