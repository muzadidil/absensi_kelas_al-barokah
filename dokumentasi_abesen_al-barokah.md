# Dokumentasi — Sistem Absensi Kelas Al-Barokah

## 1. Tujuan Proyek

Aplikasi berbasis Laravel untuk mengelola absensi dan data pengguna di lingkungan kelas Al-Barokah. Awalnya proyek ini adalah template open-source bernama **LEMS (Learner and Employee Management System)**, kemudian disesuaikan dan disederhanakan menjadi **Sistem Absensi Kelas Al-Barokah**, dengan fitur:

- Absensi harian siswa (AM IN / AM OUT / PM IN / PM OUT) via tombol biasa
- Manajemen pengguna dengan 3 role: **Admin**, **Guru**, **Learner** (siswa)
- Registrasi user baru oleh admin, lengkap dengan email selamat datang
- Dashboard ringkasan data (jumlah user, siswa, guru, log absensi, log email)

Live di: `https://al-barokah.zasha.online` (hosting: Hostinger, akses server via SSH, deploy via `git pull`).

---

## 2. Struktur Folder Penting

```
app/
├── Http/Controllers/
│   ├── AdminController.php              # Dashboard admin (hitung statistik)
│   ├── GuruController.php               # Dashboard guru + kelola daftar guru (admin)
│   ├── LearnerController.php            # Dashboard & CRUD data siswa
│   ├── RegisterController.php           # Registrasi user oleh admin
│   ├── UserController.php               # Manajemen "Pengguna Terdaftar" & kirim welcome email
│   ├── Admin/
│   │   └── LearnerAttendanceController.php  # Logika absensi (index + store)
│   └── Auth/
│       └── AuthenticatedSessionController.php # Redirect login sesuai role
├── Models/
│   ├── User.php
│   ├── Learner.php                      # Data siswa
│   ├── LearnerAttendance.php            # Data absensi harian (am_in, am_out, pm_in, pm_out)
│   └── EmailLog.php
└── Mail/
    └── WelcomeMail.php                  # Email selamat datang saat user baru dibuat

resources/views/
├── admin/
│   ├── dashboard.blade.php              # Dashboard admin (pakai layouts.admin, ada sidebar)
│   ├── register-user.blade.php          # Form tambah user baru
│   ├── guru/index.blade.php             # Daftar guru (admin) — lihat & hapus akun guru
│   ├── learners/index.blade.php         # Daftar murid (admin) — tambah/edit/hapus
│   ├── reports/index.blade.php          # Placeholder "Laporan" (Coming Soon)
│   └── attendance/index.blade.php       # Halaman absensi (dropdown + tombol), pakai layouts.admin
├── guru/dashboard.blade.php             # Dashboard guru (pakai layouts.app, TANPA sidebar admin)
├── learner/dashboard.blade.php          # Dashboard siswa (pakai layouts.app, TANPA sidebar admin)
├── layouts/
│   ├── admin.blade.php                  # Layout sidebar admin (dipakai Admin saja)
│   ├── app.blade.php / guest.blade.php  # Layout sederhana (dipakai Guru & Murid, tanpa sidebar)
└── emails/welcome.blade.php             # Template email selamat datang

routes/web.php                           # Semua route utama aplikasi
database/
├── migrations/                          # Struktur tabel (learners, learner_attendance, dll.)
└── seeders/RoleSeeder.php               # Seeder role (admin, guru, learner) + akun admin awal
```

---

## 3. Role & Alur Login

**Login memakai satu halaman yang sama untuk semua role** (`/login`) — tidak ada form login terpisah per role. Setelah login berhasil, sistem mengecek role akun dan otomatis redirect:

| Role     | Redirect setelah login | Layout & Tampilan Dashboard |
|----------|-------------------------|-------------------------------------|
| admin    | `/admin/dashboard`      | Layout `layouts.admin` (ada sidebar lengkap: Dasbor, Murid, Guru, Absensi, Pengguna Terdaftar, Laporan) + statistik & grafik |
| guru     | `/guru/dashboard`       | Layout `layouts.app` (**tanpa sidebar**) — cuma 2 kartu: Log Absensi & Profil Saya |
| learner  | `/learner/dashboard`    | Layout `layouts.app` (**tanpa sidebar**) — cuma 2 kartu: Absensi & Profil Saya |

Jadi dashboard Admin **jauh lebih lengkap** (sidebar + kelola semua data) dibanding dashboard Guru/Murid yang sengaja dibuat sangat sederhana (hanya akses ke absensi & profil sendiri).

**Cara membuat akun berbeda per role:**
- **Murid**: bisa daftar akun **sendiri** lewat halaman publik `/register` — otomatis dapat role `learner`, tidak bisa pilih role lain dari sana.
- **Guru & Admin**: **tidak bisa** membuat akun sendiri. Akunnya hanya bisa dibuat oleh Admin lewat menu "Daftarkan Pengguna" (`/register-user`), yang punya pilihan role Admin/Guru/Murid.
- Begitu akun dibuat (baik oleh admin atau daftar sendiri), pemiliknya bisa login sendiri kapan saja tanpa perlu bantuan admin lagi.

Role disimpan menggunakan package **Spatie Laravel Permission**. Role awal dibuat lewat `RoleSeeder`, yang **aman dijalankan berulang** (idempotent) — dan sejak revisi terbaru juga otomatis mengganti nama role lama `employee` menjadi `guru` jika masih ada di database produksi:

```bash
php artisan db:seed --class=RoleSeeder --force
```

---

## 4. Riwayat Perubahan (dari sesi ini)

### 4.1 Perbaikan Bug: Error 500 saat log absensi "keluar"
- **Penyebab**: kolom `am_out`/`pm_out` di tabel `learner_attendance` bertipe `TIME`, tapi kode menyimpan `Carbon::now()` (format datetime penuh) — ditolak MySQL strict mode di server produksi.
- **Perbaikan**: `LearnerAttendanceController::store()` sekarang menyimpan `now()->format('H:i:s')`.

### 4.2 Perbaikan Bug: Error 500 saat registrasi user baru
- **Penyebab**: `RegisterController` memanggil API eksternal MailboxLayer (`apilayer.net`) untuk validasi email, tapi API key (`MAILBOXLAYER_API_KEY`) tidak pernah diisi di `.env` server. Saat API gagal merespons dengan format yang diharapkan, kode mengakses key array yang tidak ada (`$response['smtp_check']`) → fatal error tak tertangani.
- **Perbaikan**: pengecekan email lewat MailboxLayer sekarang **dilewati otomatis** jika API key kosong, dan divalidasi dulu keberadaan key sebelum diakses.

### 4.3 Penyederhanaan: Registrasi user tanpa OTP
- Alur registrasi admin awalnya mengirim kode OTP ke email calon user dan butuh verifikasi terpisah sebelum akun benar-benar dibuat.
- Disederhanakan: `registerByAdmin()` sekarang **langsung membuat user** begitu form disubmit (tanpa OTP), sesuai kebutuhan pemakaian sehari-hari yang lebih praktis.
- File yang dihapus: route `/verify-otp`, method `verifyOtp()` & `showOtpForm()`, view `admin/verify-otp.blade.php`.

### 4.4 Penghapusan fitur absensi via QR Code
- Sebelumnya absensi dilakukan dengan **scan QR code** memakai kamera (library `html5-qrcode`), lengkap dengan endpoint `lookup-learner` untuk mencari siswa berdasarkan `qr_code`.
- Diganti total menjadi **dropdown pilih nama siswa + tombol submit biasa** — lebih sederhana dan tidak butuh akses kamera.
- File/kode yang dihapus: script `html5-qrcode`, elemen `#qr-reader`, method `lookupLearner()`, route `admin.attendance.lookup-learner`, serta 2 file view cadangan (`index1.blade.php`, `index3.blade.php`) yang sudah tidak terpakai.

### 4.5 Penggantian role "Employee" menjadi "Guru"
- Semua istilah **Employee** di sistem (controller, route, view, dashboard, form registrasi, role di database) diganti menjadi **Guru**, karena istilah ini lebih sesuai konteks sekolah/kelas.
- `EmployeeController` → `GuruController`, folder view `employee/` → `guru/`, route `/employee/dashboard` → `/guru/dashboard`.
- `RoleSeeder` dibuat otomatis rename role `employee` → `guru` di database produksi supaya user lama yang sudah punya role tersebut tidak perlu di-assign ulang manual.

### 4.6 Penghapusan fitur Pengumuman (Announcement) via Email
- Fitur lama: admin bisa membuat & mengirim pengumuman ke user tertentu, dengan filter berdasarkan grade/kelas, plus riwayat pengiriman (logs).
- Dihapus total: controller (`AnnouncementController`), model (`Announcement`, `AnnouncementTarget`, `AnnouncementLog`), mailable (`AnnouncementEmail`), semua view terkait, route grup `admin/announcements/*`, serta menu "Announce" di sidebar.
- Tabel database `announcements`, `announcement_targets`, `announcement_logs` ikut **dihapus** lewat migration baru (`drop_announcement_tables.php`), karena fitur ini sudah pernah live di server produksi.

### 4.7 Ganti nama brand sistem
- Nama sistem diganti dari **"Learner and Employee Management System (LEMS)"** menjadi **"Sistem Absensi Kelas Al-Barokah"** di seluruh judul halaman, sidebar, footer, dan template email.

### 4.8 Perbaikan layout sidebar admin
- Sidebar admin tidak punya `width` CSS aktif sejak awal (definisi `width: 200px` ternyata ada di dalam blok CSS yang ter-comment), sementara area konten sudah mengasumsikan sidebar selebar 200px — menyebabkan tampilan berantakan/konten terpotong. Diperbaiki dengan menambahkan `width: 200px` ke class `.sidebar` yang aktif.
- Halaman **Attendance** sebelumnya adalah file HTML mandiri (punya `<html>`/`<head>` sendiri), sehingga saat dibuka dari sidebar tampilannya jadi full-screen tanpa sidebar. Diperbaiki dengan mengubahnya jadi `@extends('layouts.admin')` seperti halaman admin lainnya.
- Label menu sidebar "Learners" diganti jadi "Murid".

### 4.9 Aktivasi tombol sidebar yang sebelumnya mati
Tiga menu sidebar sebelumnya cuma `href="#"` tanpa halaman tujuan:
- **Guru**: dibuatkan halaman baru `admin.guru.index` (daftar user dengan role guru, bisa dihapus) — `GuruController::manage()`.
- **Reports** dan **Help**: awalnya dibuatkan halaman placeholder "Coming Soon", namun keduanya **dihapus total** lagi di langkah 4.10 di bawah (kecuali Reports/Laporan yang tetap dipertahankan sebagai placeholder).

### 4.10 Terjemahkan seluruh UI admin panel ke Bahasa Indonesia
Semua label, tombol, judul halaman, pesan sukses/error, dan teks modal diterjemahkan ke Bahasa Indonesia — mencakup: sidebar & topbar, dashboard, halaman Murid, Guru, Absensi, Register User, Registered Users, Email Audit Log, Custom Email, Profile (edit/password/hapus akun), serta dashboard Guru dan Murid. Pesan flash dari controller (`LearnerController`, `UserController`, `RegisterController`, `GuruController`, `LearnerAttendanceController`) turut diterjemahkan karena tampil langsung ke pengguna.

### 4.11 Penghapusan fitur Tentang, Bantuan, Email Kustom, dan Log Audit Email
Setelah dievaluasi, 4 menu berikut dihapus **total** (bukan cuma disembunyikan) karena dianggap tidak diperlukan:
- **Tentang**: modal "About" beserta link menunya dihapus dari `layouts/admin.blade.php`.
- **Bantuan**: halaman placeholder, route `admin.help`, dan link menu dihapus.
- **Email Kustom**: `UserController::customEmailForm()`/`sendCustomEmail()`, Mailable `CustomMessageMail`, view `custom-email.blade.php`, route `email.custom.*`, dan link menu dihapus.
- **Log Audit Email**: `EmailLogController`, view `email_logs/index.blade.php`, route `email.logs`, dan link menu dihapus.

Catatan: model `EmailLog` dan tabel `email_logs` **tetap dipertahankan** karena masih dipakai untuk logging saat registrasi user & fitur "Send Email to Selected" di halaman Registered Users (`UserController::sendMail()`), serta ditampilkan sebagai statistik "Total Log Email" di dashboard admin — hanya halaman *daftar log*-nya saja yang dihapus.

Sidebar admin sekarang tersisa: **Dasbor, Murid, Guru, Absensi, Pengguna Terdaftar, Laporan**.

---

## 5. Cara Deploy Perubahan ke Server (Hostinger via SSH)

Karena hosting hanya bisa diakses lewat SSH (tanpa akses `tinker`/`proc_open`), alur deploy standar untuk proyek ini:

```bash
cd ~/domains/al-barokah.zasha.online/app
git pull origin main
php artisan migrate --force        # jika ada migration baru
php artisan db:seed --class=RoleSeeder --force   # jika ada perubahan role
php artisan optimize:clear         # wajib, agar cache/OPcache tidak pakai kode lama
```

**Debugging error 500 di server**: cek `storage/logs/laravel.log` (bukan file per-tanggal, satu file terus bertambah). Kalau setelah retry tidak ada baris baru di log, biasanya berarti request tidak sampai ke Laravel (cache browser/CDN) atau OPcache masih menyimpan kode lama — jalankan `php artisan optimize:clear` lalu ulangi.

---

## 6. Catatan Teknis Lain

- **Kolom `qr_code` di tabel `learners`** masih ada di database (tidak dihapus), meski fiturnya sudah tidak dipakai — aman dibiarkan, tidak mengganggu.
- **`UserController::sendMail()`** (fitur "Kirim Email ke Terpilih" di halaman Pengguna Terdaftar, mengirim ulang welcome email) **tetap ada** — ini berbeda dari fitur "Email Kustom" (`customEmailForm()`/`sendCustomEmail()`) yang sudah dihapus total di bagian 4.11.
- **`WelcomeMail`** dan **`EmailLog`** juga terpisah dari fitur-fitur yang sudah dihapus (Announcement, Email Kustom, Log Audit Email) — dipakai khusus saat registrasi user baru & fitur kirim ulang welcome email, tidak ikut terhapus.
- **Locale aplikasi** (`config/app.php` → `APP_LOCALE`) masih default `en`. Terjemahan UI ke Bahasa Indonesia (bagian 4.10) dilakukan dengan mengganti teks langsung di setiap file Blade/controller, **bukan** lewat sistem i18n Laravel (`resources/lang`) — jadi kalau ke depan butuh multi-bahasa yang proper, perlu direfaktor ke sistem lang file.
