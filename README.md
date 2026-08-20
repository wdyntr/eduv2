# Lampung Belajar

Platform pembelajaran digital untuk ekosistem pendidikan Provinsi Lampung — menghimpun materi ajar, direktori kelas Google Classroom sekolah, artikel budaya Lampung, dan publikasi jurnal ilmiah guru dalam satu portal, dengan tata kelola akses berbasis role & permission (RBAC).

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2)
- **Autentikasi & sesi:** Laravel session auth (`Auth::attempt()`) — bukan token API
- **RBAC:** [spatie/laravel-permission](https://spatie.be/docs/laravel-permission) — role & permission dinamis, dikelola lewat UI (bukan hardcode)
- **Database:** MySQL/MariaDB
- **Frontend:** Blade + vanilla JS (fetch API) + Bootstrap 5, di-*build* lewat Vite

## Fitur Utama

| Modul | Ringkasan |
|---|---|
| **Materi Pembelajaran** | Video (embed YouTube), PPT/PDF (tautan eksternal), dikelompokkan per jenjang (SMA/SMK/SLB) dan mata pelajaran |
| **Classroom** | Direktori sekolah se-Lampung, dengan link Google Classroom **per sekolah per mata pelajaran**. Kerangka sinkronisasi statistik (jumlah guru/siswa/task/materi) via Google Classroom API sudah siap, menunggu penyediaan Google Workspace |
| **Artikel Budaya** | Artikel & video seputar sejarah, adat, dan tradisi Lampung, dikelompokkan per kategori |
| **Jurnal Ilmiah** | Alur pengajuan → review → publikasi jurnal guru, dengan riwayat revisi tersimpan (skema `jurnal` / `jurnal_revisi` / `jurnal_review`) dan pembersihan berkas otomatis saat file diganti/jurnal dihapus |
| **Kelola User & Role** | CRUD akun serta role+permission sepenuhnya dari UI — role baru otomatis dapat menu & akses sesuai permission yang diberikan, tanpa ubah kode |

## Role & Permission

| Role | Permission utama | Catatan |
|---|---|---|
| Pengunjung (publik) | – | Tanpa akun; akses baca ke materi, classroom, artikel, dan jurnal yang sudah disetujui |
| **penulis** | `jurnal.ajukan` | Mengajukan & merevisi jurnal |
| **operator_konten** | `materi.kelola`, `artikel.kelola` | Kelola materi & artikel budaya |
| **sekolah** | `classroom.kelola` (dibatasi ke `sekolah_id` sendiri) | Hanya bisa kelola classroom sekolahnya |
| **reviewer_jurnal** | `jurnal.lihat`, `jurnal.review` | Approve/reject & isi metadata publikasi jurnal |
| **admin_sistem** | `users.kelola`, `sistem.kelola`, `classroom.kelola`, `jurnal.lihat` | Kelola user, kelola role, monitoring classroom lintas sekolah; sengaja **tidak** bisa review jurnal maupun kelola konten |

Role & permission baru bisa ditambahkan lewat halaman **Kelola Role** — seluruh guard akses di controller memakai pengecekan permission (`can('nama.permission')`), bukan nama role, sehingga penambahan role tidak memerlukan perubahan kode.

## Instalasi

```bash
git clone <url-repo>
cd edulampung
composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env`, lalu:

```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=AdminUserSeeder   # akun admin_sistem awal
```

Jalankan:

```bash
php artisan serve
```

<!-- ## Integrasi Google Classroom (opsional, belum aktif)

Statistik guru/siswa/task/materi per kelas disiapkan untuk sinkron otomatis dari Google Classroom API, tapi baru bisa dipakai setelah:

1. Google Workspace domain untuk sekolah-sekolah sudah dibuat, dengan super admin yang bisa mengaktifkan **domain-wide delegation**.
2. `composer require google/apiclient`.
3. Kredensial *service account* ditaruh di `storage/app/google-service-account.json`.
4. Isi `.env`: `GOOGLE_CLASSROOM_ADMIN_EMAIL=...`
5. Jadwalkan/perintah manual: `php artisan classroom:sync-stats` -->

Cron untuk scheduler Laravel (wajib untuk sinkronisasi otomatis tiap malam):

```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Struktur Basis Data (ringkas)

- `users`, `roles`, `permissions`, `model_has_roles`, `role_has_permissions` — akun & RBAC (Spatie)
- `sekolah`, `sekolah_kelas`, `classroom_kelas_stats` — direktori sekolah, link classroom per mapel, cache statistik sinkronisasi
- `jenjang`, `kota_kabupaten`, `mata_pelajaran` — tabel referensi
- `materi` — konten pembelajaran
- `artikel`, `artikel_kategori` — artikel budaya
- `jurnal`, `jurnal_kategori`, `jurnal_revisi`, `jurnal_review` — pengajuan jurnal beserta riwayat revisi & keputusan review
<!-- 
## Catatan Pengembangan

- Semua halaman `/admin/*` dilindungi middleware `auth` (session Laravel) + pengecekan permission per route/controller.
- Fetch API di sisi admin otomatis menyertakan header CSRF lewat wrapper di `resources/views/user/layouts/base.blade.php` — tidak perlu ditambahkan manual di tiap file JS.
- Berkas upload (jurnal) tersimpan di `storage/app/...` (path diatur lewat `config('jurnal.upload_path')`), dengan pembersihan otomatis lewat event model saat revisi/jurnal dihapus. -->