{{-- resources/views/admin/users/index.blade.php --}}
@extends('admin.layout')
@section('title', 'Kelola User')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-subtitle">Daftar semua pengguna sistem</h2>
    </div>
    <button class="btn-primary" onclick="openModal('modal-add-user')">
        + Tambah User
    </button>
</div>

{{-- Filter --}}
<form method="GET" class="filter-row">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Cari nama, username, no. induk..." class="admin-input">
    <select name="role" class="admin-select">
        <option value="">Semua Role</option>
        <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
    </select>
    <select name="kelas" class="admin-select">
        <option value="">Semua Kelas</option>
        @foreach($kelasList as $k)
            <option value="{{ $k }}" {{ request('kelas') === $k ? 'selected' : '' }}>{{ $k }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">Filter</button>
    <a href="{{ route('admin.users.index') }}" class="btn-ghost">Reset</a>
</form>

{{-- Table --}}
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>No. Induk</th>
                <th>Kelas</th>
                <th>Role</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="td-name">{{ $user->name }}</td>
                <td class="td-mono">{{ $user->username }}</td>
                <td class="td-mono">{{ $user->no_induk ?? '-' }}</td>
                <td>{{ $user->kelas ?? '-' }}</td>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'badge-gold' : 'badge-muted' }}">
                        {{ $user->role }}
                    </span>
                </td>
                <td>
                    <form method="POST"
                          action="{{ route('admin.users.toggle-active', $user) }}"
                          style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}"
                                style="border:none;cursor:pointer;">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </form>
                </td>
                <td class="td-actions">
                    <button class="btn-icon" onclick="openEdit({{ $user->id }})">Edit</button>
                    <form method="POST"
                          action="{{ route('admin.users.destroy', $user) }}"
                          onsubmit="return confirm('Hapus user ini?')"
                          style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon btn-icon-danger">Hapus</button>
                    </form>
                </td>
            </tr>

            {{-- Edit row (inline) --}}
            <tr class="edit-row" id="edit-row-{{ $user->id }}" style="display:none;">
                <td colspan="7">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="inline-form">
                        @csrf @method('PUT')
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="name" value="{{ $user->name }}" class="admin-input" required>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" value="{{ $user->username }}" class="admin-input" required>
                            </div>
                            <div class="form-group">
                                <label>Password Baru <small>(kosongkan jika tidak diubah)</small></label>
                                <input type="password" name="password" class="admin-input">
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="admin-select">
                                    <option value="siswa" {{ $user->role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kelas</label>
                                <input type="text" name="kelas" value="{{ $user->kelas }}" class="admin-input">
                            </div>
                            <div class="form-group">
                                <label>No. Induk</label>
                                <input type="text" name="no_induk" value="{{ $user->no_induk }}" class="admin-input">
                            </div>
                        </div>
                        <div style="display:flex;gap:10px;margin-top:12px;">
                            <button type="submit" class="btn-primary">Simpan</button>
                            <button type="button" class="btn-ghost" onclick="closeEdit({{ $user->id }})">Batal</button>
                        </div>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $users->links('admin.partials.pagination') }}

{{-- Modal tambah user --}}
<div class="modal-overlay" id="modal-add-user" onclick="closeModalOutside(event, 'modal-add-user')">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Tambah User</h3>
            <button onclick="closeModal('modal-add-user')" class="drawer-close">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" name="name" class="admin-input" required>
                </div>
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" class="admin-input" required>
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="admin-input" required>
                </div>
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" class="admin-select" id="add-role-select"
                            onchange="toggleSiswaFields(this.value)">
                        <option value="siswa">Siswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group siswa-only">
                    <label>Kelas</label>
                    <input type="text" name="kelas" class="admin-input" placeholder="contoh: XII IPA 1">
                </div>
                <div class="form-group siswa-only">
                    <label>No. Induk</label>
                    <input type="text" name="no_induk" class="admin-input">
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;justify-content:flex-end;">
                <button type="button" class="btn-ghost" onclick="closeModal('modal-add-user')">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection