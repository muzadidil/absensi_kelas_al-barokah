# Analisa Teknis Lengkap — Sistem Absensi Kelas Al-Barokah

> Dokumen ini adalah **snapshot teknis menyeluruh** dari kondisi kode saat ini (bukan riwayat perubahan). Ditulis supaya AI/developer lain yang belum pernah membuka proyek ini bisa langsung paham arsitektur, skema data, dan titik-titik rawan — tanpa perlu menjelajah kode dari nol.
>
> Untuk riwayat perubahan per sesi kerja sebelumnya, lihat [`dokumentasi_abesen_al-barokah.md`](dokumentasi_abesen_al-barokah.md). Untuk instruksi instalasi generik (sisa template LEMS), lihat [`README.md`](README.md) — sebagian isinya sudah usang (nama proyek lama, screenshot lama).

---

## 1. Apa Aplikasi Ini

Aplikasi Laravel untuk mengelola **absensi harian murid** dan **data pengguna** (Admin/Guru/Murid) di lingkungan kelas Al-Barokah. Awalnya fork dari template open-source **LEMS (Learner and Employee Management System)**, lalu disederhanakan drastis: fitur QR-code, pengumuman email, OTP registrasi, dan beberapa halaman admin dihapus; seluruh UI diterjemahkan ke Bahasa Indonesia; role "Employee" diganti "Guru".

- **Live**: `https://al-barokah.zasha.online`
- **Hosting**: Hostinger shared hosting, akses **hanya via SSH** (`proc_open` mati → `php artisan tinker` tidak bisa dipakai di server)
- **Deploy**: `git pull` manual di server (lihat §8)

---

## 2. Tech Stack

| Layer | Teknologi |
|---|---|
| Framework | Laravel 12 (PHP ^8.2) |
| Auth scaffolding | Laravel Breeze (Blade, bukan Inertia/React) |
| Role & permission | `spatie/laravel-permission` ^6.19 |
| Frontend admin | Bootstrap 5 + Bootstrap Icons + Chart.js + SweetAlert2 — **semua via CDN**, tidak lewat build Vite |
| Frontend auth pages | Tailwind CSS (via Vite, `resources/css/app.css` cuma 3 baris `@tailwind`) |
| Build tool | Vite 6 + `laravel-vite-plugin` (dipakai untuk halaman auth/Breeze saja) |
| Database | MySQL di produksi (Hostinger); `.env.example` default ke SQLite untuk lokal |
| Testing | PHPUnit 11 (hanya test bawaan Breeze, lihat §7) |

**Catatan penting untuk siapa pun yang mau develop lokal**: layout admin (`layouts/admin.blade.php`) tidak butuh `npm run build` sama sekali karena semua asetnya CDN — cukup `composer install` + DB + `php artisan serve`. Tapi karena `LearnerController::index()` memakai `orderByRaw("CONCAT(...))")` (fungsi MySQL, tidak ada di SQLite), **gunakan MySQL juga untuk dev lokal** kalau mau membuka halaman Murid tanpa error — SQLite akan lempar `no such function: CONCAT`.

---

## 3. Struktur Folder

```
app/
├── Http/Controllers/
│   ├── AdminController.php              # Dashboard admin: hitung statistik (userCount, learnerCount, guruCount, mailLogCount, attendanceCount)
│   ├── GuruController.php               # Dashboard guru (statis) + index/destroy daftar guru (admin)
│   ├── LearnerController.php            # Dashboard murid + full CRUD data murid (admin)
│   ├── UserController.php               # Daftar "Pengguna Terdaftar", update role, kirim ulang welcome email
│   ├── RegisterController.php           # 2 alur registrasi: publik (learner) & oleh-admin (semua role)
│   ├── ProfileController.php            # Edit profil, ganti password, hapus akun (dipakai admin & user biasa)
│   ├── Admin/
│   │   ├── LearnerAttendanceController.php  # Absensi: index (list hari ini) + store (catat 1 sesi)
│   │   └── ClassSettingController.php       # CRUD Tingkat Kelas (GradeLevel) & Tahun Ajaran (Section)
│   └── Auth/  (7 file)                  # Breeze default: login, password reset, email verification, dst.
├── Models/
│   ├── User.php                         # HasRoles (Spatie), MustVerifyEmail
│   ├── Learner.php                      # fname/mname/lname/email/grade_level/section (string, bukan FK!) + qr_code (legacy, tak dipakai)
│   ├── LearnerAttendance.php            # belongsTo Learner; am_in/am_out/pm_in/pm_out (time)
│   ├── GradeLevel.php                   # cuma { name }
│   ├── Section.php                      # cuma { name } — sebenarnya dipakai sebagai "Tahun Ajaran", bukan "kelompok/section" harfiah
│   └── EmailLog.php                     # belongsTo User; audit trail welcome email
└── Mail/
    └── WelcomeMail.php                  # Satu-satunya Mailable yang tersisa

resources/views/
├── admin/
│   ├── dashboard.blade.php              # @extends('layouts.admin') — baru saja di-redesign (lihat §9)
│   ├── learners/index.blade.php         # Daftar + modal tambah/edit murid
│   ├── guru/index.blade.php             # Daftar guru, tombol hapus
│   ├── class-settings/index.blade.php   # CRUD Tingkat Kelas & Tahun Ajaran
│   ├── attendance/index.blade.php       # Pilih murid (dropdown) + pilih sesi + tabel hari ini
│   ├── register-user.blade.php          # Form admin daftarkan user baru (pilih role)
│   ├── reports/index.blade.php          # Placeholder "Segera Hadir"
│   └── profile/*.blade.php              # Edit profil versi admin (dipakai lewat layouts.admin)
├── guru/dashboard.blade.php             # @extends('layouts.app') — TANPA sidebar, sangat minimal
├── learner/dashboard.blade.php          # @extends('layouts.app') — TANPA sidebar, sangat minimal
├── users/index.blade.php                # "Pengguna Terdaftar" — daftar semua User + ganti role + kirim welcome email ulang
├── users/oldindex.blade.php             # ⚠️ file mati, tidak direferensikan route manapun
├── emails/welcome.blade.php             # Template email yang benar-benar dipakai
├── emails/custom-message.blade.php      # ⚠️ file mati — Mailable-nya (`CustomMessageMail`) sudah dihapus, view-nya tertinggal
└── layouts/
    ├── admin.blade.php                  # Sidebar gradient + topbar, dipakai HANYA oleh Admin
    ├── app.blade.php / guest.blade.php  # Layout Breeze standar (Tailwind), dipakai Guru/Murid & halaman auth
    └── navigation.blade.php             # Navbar Breeze default (nyaris tidak kepakai karena admin punya topbar sendiri)

routes/
├── web.php     # Semua route aplikasi (lihat §6 untuk peta lengkap)
└── auth.php    # Route Breeze (login/register/password), register.form & register.submit dioverride ke RegisterController custom

database/
├── migrations/  # 15 file, urutan penting (lihat §5)
└── seeders/
    ├── DatabaseSeeder.php   # default `php artisan db:seed` — HANYA buat 1 User factory tanpa role (lihat catatan §10)
    └── RoleSeeder.php       # idempotent, buat 3 role + user admin `test@example.com` — WAJIB dijalankan terpisah
```

---

## 4. Peran & Alur Autentikasi

Satu halaman login (`/login`) untuk semua role. Setelah login, `GET /dashboard` redirect otomatis berdasarkan role (lihat `routes/web.php` baris ~30):

| Role | Redirect | Layout | Isi Dashboard |
|---|---|---|---|
| `admin` | `/admin/dashboard` | `layouts.admin` (sidebar lengkap) | 5 stat card + 2 chart + akses ke semua modul |
| `guru` | `/guru/dashboard` | `layouts.app` (tanpa sidebar) | Statis, sangat minim (belum ada fitur guru sungguhan) |
| `learner` | `/learner/dashboard` | `layouts.app` (tanpa sidebar) | Statis, sangat minim |

**Cara akun dibuat:**
- **Murid**: bisa daftar sendiri lewat `/register` (publik) → otomatis role `learner`, langsung lewat alur verifikasi email Laravel bawaan (`sendEmailVerificationNotification`).
- **Admin/Guru**: hanya bisa dibuat lewat `/register-user` (perlu login dulu — tapi **tidak ada pengecekan role**, jadi siapa pun yang login, termasuk Guru/Murid, bisa akses form ini dan membuat akun Admin baru! Lihat temuan §7.2).

Role disimpan via Spatie (`model_has_roles`, dst — migration `2025_06_03_134108_create_permission_tables.php`).

---

## 5. Skema Database (state saat ini, setelah semua migrasi)

```
users                    — id, name, email(unique), password, email_verified_at, timestamps
  ↳ model_has_roles       — Spatie: user_id ↔ role_id (role: admin | guru | learner)

learners                 — id, fname, mname, lname, email(unique, NULLABLE ✅ baru), grade_level(string),
                            section(string), qr_code(unique, nullable — LEGACY, tak dipakai lagi), timestamps
  ⚠️ grade_level & section adalah STRING BEBAS, bukan foreign key ke grade_levels/sections.
     Validasi controller memang mengecek `exists:grade_levels,name` / `exists:sections,name` saat
     create/update, TAPI tidak ada FK di database — kalau grade_levels.name diganti/dihapus lewat
     ClassSettingController, data lama di learners.grade_level TIDAK ikut ter-update (data jadi yatim).

learner_attendance        — id, learner_id(FK→learners, cascade delete), date, am_in/am_out/pm_in/pm_out(time,
                            nullable), timestamps
                            Satu baris per (learner_id, date) — dijamin oleh logika `firstOrCreate` di controller,
                            BUKAN oleh unique constraint di database.

grade_levels               — id, name(unique), timestamps      [Tingkat Kelas, mis. "7", "8"]
sections                   — id, name(unique), timestamps      [ditampilkan sbg "Tahun Ajaran", mis. "2026/2027"]

email_logs                 — id, user_id(FK→users, cascade), email, subject, sent_at, timestamps
                            Audit log untuk welcome email (baru & kirim ulang). TIDAK dipakai untuk fitur lain.

--- tabel yang SUDAH DIHAPUS (migration drop_announcement_tables) ---
announcements, announcement_targets, announcement_logs   — bekas fitur "Pengumuman Email", dihapus total
```

Tabel bawaan Laravel standar (`cache`, `jobs`, `sessions`, `password_reset_tokens`, dll.) juga ada tapi tidak relevan didiskusikan.

---

## 6. Peta Route Lengkap (routes/web.php + auth.php)

**Publik (tanpa login):**
- `GET /` — welcome page (redirect ke dashboard kalau sudah login, karena middleware `guest`)
- `GET|POST /register`, `/login`, `/forgot-password`, `/reset-password/{token}` — Breeze standar
- ⚠️ **`GET|POST|PUT|DELETE /learners...` (resource penuh) — lihat temuan kritis §7.1**

**Perlu login (`auth`, `verified`), tanpa cek role spesifik (siapa pun yang login bisa akses — lihat §7.2):**
- `GET /register-user`, `POST /register-user` — daftarkan user baru (pilih role apa saja)
- `GET /admin/dashboard`, `/guru/dashboard`, `/learner/dashboard`
- `GET /admin/guru`, `DELETE /admin/guru/{user}`
- `GET /admin/reports` (placeholder)
- `GET /users`, `PUT /users/{user}`, `DELETE /users/{user}`, `POST /users/sendmail`
- `GET|PATCH|DELETE /profile`, plus varian `/admin/profile/*`
- `GET /attendance`, `POST /attendance/store`
- `GET /admin/class-settings`, CRUD grade-levels & sections

Tidak ada middleware `role:admin` di manapun dalam kode ini — package `spatie/laravel-permission` sudah ter-install lengkap dengan middleware-nya, tapi **tidak dipakai sama sekali** untuk membatasi akses per-role. Semua pembatasan role saat ini murni kosmetik (menu sidebar disembunyikan), bukan proteksi di level route/controller.

---

## 7. Temuan Teknis (urutan berdasarkan tingkat risiko)

### 7.1 🔴 KRITIS — Data murid bisa diakses & diubah TANPA LOGIN

```php
// routes/web.php — di LUAR blok Route::middleware(['auth','verified'])->group(...)
Route::resource('learners', LearnerController::class)->names('admin.learners');
Route::delete('/learners/{id}', [LearnerController::class, 'destroy'])->name('learners.destroy');
```

Dikonfirmasi lewat `php artisan route:list -v`: kedelapan route resource murid (index/create/store/show/edit/update/destroy) **hanya punya middleware `web`**, tidak ada `auth`. Komentar di kode bahkan mengakui ini sengaja: `// Temporarily allow public access for testing purposes~`. Ada blok kode terkomentar tepat di bawahnya yang menunjukkan versi "seharusnya" (`role:admin`), tapi tidak pernah diaktifkan.

**Dampak**: siapa pun yang tahu URL `https://al-barokah.zasha.online/learners` bisa melihat, menambah, mengubah, dan **menghapus** data pribadi murid (nama lengkap, email, kelas) tanpa login sama sekali. Ini live di produksi sekarang.

**Rekomendasi**: pindahkan `Route::resource('learners', ...)` ke dalam blok `Route::middleware(['auth', 'verified'])->group(...)` yang sudah ada, atau tambahkan middleware secara eksplisit. Perbaikan 1 baris, dampak besar — sebaiknya jadi prioritas #1 sebelum hal lain.

### 7.2 🟠 TINGGI — Tidak ada pembatasan role di route/controller manapun

Semua route yang butuh login hanya dicek `auth` + `verified`, tidak pernah `role:admin`/`role:guru`. Artinya user dengan role `learner` yang login bisa, misalnya, membuka `/register-user` dan mendaftarkan akun baru dengan role `admin`, atau membuka `/admin/guru` dan menghapus akun guru manapun — cukup dengan mengetik URL-nya langsung (menu sidebar yang disembunyikan hanya proteksi UI, bukan proteksi akses). Spatie Permission sudah ter-install dan siap pakai (`Route::middleware('role:admin')`), tinggal diterapkan.

### 7.3 🟡 SEDANG — `grade_level`/`section` di tabel `learners` bukan foreign key

Disimpan sebagai string bebas, divalidasi lewat `exists:grade_levels,name` saat input tapi **tidak ada FK constraint**. Kalau nama tingkat kelas diedit lewat menu "Kelas & Tahun Ajaran" (mis. "7" → "VII"), semua data murid yang sudah punya `grade_level = "7"` tidak ikut berubah dan jadi tidak cocok lagi dengan opsi dropdown manapun. Idealnya `learners` punya `grade_level_id`/`section_id` sebagai FK.

### 7.4 🟡 SEDANG — Tidak ada test otomatis untuk logika inti aplikasi

`tests/Feature/` isinya cuma test bawaan Breeze (login, register, password reset, email verification) — nol test untuk `LearnerController`, `LearnerAttendanceController`, `ClassSettingController`, atau `GuruController`. Perubahan pada logika absensi/CRUD murid saat ini hanya bisa diverifikasi manual lewat browser.

### 7.5 🟢 RENDAH — Sisa file/kode mati dari fitur yang sudah dihapus
- `resources/views/emails/custom-message.blade.php` — Mailable `CustomMessageMail` sudah dihapus, view-nya tertinggal.
- `resources/views/users/oldindex.blade.php` — tidak direferensikan route manapun.
- `RegisterController::register()` — ada ~40 baris blok kode terkomentar (versi lama method yang sama) tertinggal di file.
- Kolom `learners.qr_code` masih ada di skema meski fitur QR sudah dihapus total — aman tapi mubazir.

### 7.6 🟢 RENDAH — `DatabaseSeeder` dan `RoleSeeder` tidak terhubung
`php artisan db:seed` (default, menjalankan `DatabaseSeeder`) hanya membuat `User::factory()` dengan email `test@example.com` **tanpa role apa pun** — user ini akan kena `abort(403)` di `/dashboard`. Setup yang benar untuk proyek ini justru `php artisan db:seed --class=RoleSeeder` (lihat §8), yang kebetulan juga membuat user dengan email yang sama tapi lewat `firstOrCreate` + auto-assign role admin. Menjalankan keduanya rawan bikin bingung developer baru; sebaiknya `DatabaseSeeder::run()` cukup memanggil `$this->call(RoleSeeder::class);`.

### 7.7 🟢 RENDAH — Locale aplikasi tidak konsisten dengan UI
`config/app.php` → `APP_LOCALE` masih default `en`, padahal seluruh teks UI sudah diterjemahkan manual ke Bahasa Indonesia langsung di setiap file Blade/controller (bukan lewat `resources/lang`). Tidak masalah untuk saat ini (single-language), tapi kalau nanti butuh multi-bahasa perlu direfaktor total ke sistem lang file Laravel.

---

## 8. Cara Deploy ke Produksi (Hostinger via SSH)

```bash
cd ~/domains/al-barokah.zasha.online/app
git pull origin main
php artisan migrate --force        # kalau ada migration baru
php artisan db:seed --class=RoleSeeder --force   # kalau ada perubahan role
php artisan optimize:clear         # WAJIB — kalau tidak, OPcache/config cache bisa masih pakai kode lama
```

`php artisan tinker` **tidak berfungsi** di server ini (`proc_open` dimatikan hosting) — segala kebutuhan seed data harus lewat file seeder yang di-commit, bukan dieksekusi ad-hoc.

Debug error 500 di server: cek `storage/logs/laravel.log` (satu file terus bertambah, bukan per-tanggal).

---

## 9. Pekerjaan Terbaru (ringkas — detail lengkap ada di riwayat commit & `dokumentasi_abesen_al-barokah.md`)

- **Redesain visual dashboard admin**: sidebar gradient gelap, topbar bersih, stat card dengan icon badge, grid 3 kolom, warna chart yang sudah divalidasi accessibility (CVD-safe). Menyentuh `layouts/admin.blade.php` dan `admin/dashboard.blade.php`.
- **Email murid dijadikan opsional**: kolom `learners.email` sekarang `nullable` (migration `2026_07_24_084340_make_email_nullable_on_learners_table.php`), validasi controller diubah dari `required` ke `nullable`, string kosong dinormalisasi jadi `NULL` (bukan `""`) supaya banyak murid tanpa email tidak bentrok dengan unique index.
- Sebelum itu (sesi-sesi lain): penghapusan QR absensi, penghapusan fitur pengumuman email, penggantian role Employee→Guru, penerjemahan penuh ke Bahasa Indonesia, penambahan CRUD Tingkat Kelas & Tahun Ajaran. Detail lengkap tiap perubahan ada di `dokumentasi_abesen_al-barokah.md` §4.

---

## 10. Ringkasan Prioritas kalau Mau Ditindaklanjuti

1. **Tutup akses publik ke `/learners`** (§7.1) — 1 baris kode, risiko kebocoran data anak-anak.
2. **Tambahkan middleware `role:admin`** ke seluruh route area admin (§7.2).
3. Pertimbangkan FK untuk `grade_level`/`section` di tabel `learners` (§7.3) sebelum data makin banyak.
4. Tulis test dasar untuk `LearnerController` & `LearnerAttendanceController` (§7.4) — dua modul paling sering diubah.
5. Bersih-bersih file mati (§7.5) — kosmetik, bisa nunggu.
