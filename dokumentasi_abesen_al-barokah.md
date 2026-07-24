# Dokumentasi — Sistem Absensi Kelas Al-Barokah

## 1. Tujuan Proyek

Aplikasi berbasis Laravel untuk mengelola absensi dan data pengguna di lingkungan kelas Al-Barokah. Awalnya proyek ini adalah template open-source bernama **LEMS (Learner and Employee Management System)**, kemudian disesuaikan dan disederhanakan menjadi **Sistem Absensi Kelas Al-Barokah**, dengan fitur:

- Absensi harian siswa (AM IN / AM OUT / PM IN / PM OUT) via tombol biasa
- Manajemen pengguna dengan 3 role: **Admin**, **Guru**, **Learner** (siswa)
- Registrasi user baru oleh admin, lengkap dengan email selamat datang
- Kelola Tingkat Kelas & Tahun Ajaran (CRUD, tidak lagi hardcode)
- **Sistem Tugas/Assignment**: admin buat tugas (pilgan + essay), assign ke kelas/individu, murid kerjakan, admin nilai essay
- **Rapor murid**: rekap nilai tugas per murid (rata-rata persen & predikat)
- **Login Murid via PIN** (terpisah dari login Admin/Guru yang pakai email+password)
- Dashboard ringkasan data (jumlah user, siswa, guru, log absensi, log email)

> ⚠️ Bagian fitur Tugas/Rapor/Login PIN (poin 3 dan seterusnya di atas) dikembangkan **di luar sesi pendampingan ini** (kemungkinan oleh developer/sesi lain langsung ke GitHub) dan baru diketahui saat `git pull`. Detailnya didokumentasikan di bagian 4.12 berdasarkan hasil penelusuran kode, bukan dari keputusan yang dibahas bersama.

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
│   │   ├── LearnerAttendanceController.php  # Logika absensi (index + store)
│   │   ├── ClassSettingController.php       # CRUD Tingkat Kelas & Tahun Ajaran
│   │   ├── AssignmentController.php         # (baru) Admin kelola tugas + assign ke murid
│   │   ├── AssignmentQuestionController.php # (baru) Admin kelola soal per tugas
│   │   └── RaportController.php             # (baru) Rapor semua murid (admin)
│   ├── Learner/
│   │   └── AssignmentController.php     # (baru) Murid lihat/kerjakan tugas + lihat rapor sendiri
│   └── Auth/
│       ├── AuthenticatedSessionController.php # Redirect login sesuai role (Admin/Guru)
│       └── LearnerLoginController.php   # (baru) Login Murid pakai PIN (terpisah dari Breeze)
├── Models/
│   ├── User.php
│   ├── Learner.php                      # Data siswa (nama_lengkap, email nullable, pin, grade_level, section)
│   ├── LearnerAttendance.php            # Data absensi harian (am_in, am_out, pm_in, pm_out)
│   ├── GradeLevel.php / Section.php     # Master data Tingkat Kelas & Tahun Ajaran
│   ├── Assignment.php                   # (baru) Data tugas
│   ├── AssignmentQuestion.php           # (baru) Soal per tugas (pilgan/essay)
│   ├── AssignmentLearner.php            # (baru) Pivot: tugas ditugaskan ke murid mana, status & skor
│   ├── LearnerAnswer.php                # (baru) Jawaban murid per soal
│   └── EmailLog.php
├── Http/Middleware/
│   └── LearnerAuth.php                  # (baru) Middleware alias `auth.learner`, cek session('learner_id')
└── Mail/
    └── WelcomeMail.php                  # Email selamat datang saat user baru dibuat

resources/views/
├── admin/
│   ├── dashboard.blade.php              # Dashboard admin (pakai layouts.admin, ada sidebar)
│   ├── register-user.blade.php          # Form tambah user baru
│   ├── guru/index.blade.php             # Daftar guru (admin) — lihat & hapus akun guru
│   ├── learners/index.blade.php         # Daftar murid (admin) — tambah/edit/hapus
│   ├── class-settings/index.blade.php   # Kelola Tingkat Kelas & Tahun Ajaran
│   ├── assignments/*.blade.php          # (baru) Kelola tugas, soal, lihat & nilai jawaban murid
│   ├── raport/*.blade.php               # (baru) Rapor semua murid & detail per murid
│   ├── reports/index.blade.php          # Placeholder "Laporan" (Coming Soon)
│   └── attendance/index.blade.php       # Halaman absensi (dropdown + tombol), pakai layouts.admin
├── guru/dashboard.blade.php             # Dashboard guru (pakai layouts.app, TANPA sidebar admin)
├── learner/
│   ├── dashboard.blade.php              # Dashboard murid (didesain ulang, pakai layouts.learner)
│   ├── assignments/*.blade.php          # (baru) Daftar & kerjakan tugas
│   └── raport.blade.php                 # (baru) Rapor murid sendiri
├── layouts/
│   ├── admin.blade.php                  # Layout sidebar admin (dipakai Admin saja)
│   ├── learner.blade.php                # (baru) Layout khusus Murid (topbar/sidebar sendiri, beda dari admin)
│   ├── app.blade.php / guest.blade.php  # Layout sederhana (dipakai Guru, tanpa sidebar)
└── emails/welcome.blade.php             # Template email selamat datang

routes/web.php                           # Semua route utama aplikasi
database/
├── migrations/                          # Struktur tabel (learners, learner_attendance, assignments, dll.)
└── seeders/
    ├── RoleSeeder.php                   # Seeder role (admin, guru, learner) + akun admin awal
    └── GradeLevelSectionSeeder.php       # Seed data awal Tingkat Kelas & Tahun Ajaran
```

---

## 3. Role & Alur Login

> ⚠️ **Update penting**: sejak fitur baru ditambahkan (lihat bagian 4.12), alur login **Murid sudah berbeda total** dari Admin/Guru — bukan lagi satu halaman login yang sama untuk semua role. Bagian di bawah ini sudah disesuaikan dengan kondisi terbaru.

### Login Admin & Guru (tetap seperti semula)
Admin dan Guru login lewat tab "Admin/Guru" di halaman `/login`, pakai **email + password** (Laravel Breeze, guard `web` standar via `Auth::user()`). Setelah login, sistem mengecek role dan redirect:

| Role     | Redirect setelah login | Layout & Tampilan Dashboard |
|----------|-------------------------|-------------------------------------|
| admin    | `/admin/dashboard`      | Layout `layouts.admin` (sidebar lengkap: Dasbor, Murid, Kelas & Tahun Ajaran, Guru, Absensi, Pengguna Terdaftar, Laporan) + statistik & grafik |
| guru     | `/guru/dashboard`       | Layout `layouts.app` (**tanpa sidebar**) — cuma 2 kartu: Log Absensi & Profil Saya |

### Login Murid (BARU: pakai PIN, bukan email/password)
Murid login lewat tab **"Siswa"** di halaman `/login` yang sama secara visual, tapi mekanismenya beda total:
1. Pilih **Tingkat Kelas** → dropdown nama murid terisi otomatis via AJAX (`GET /api/learners-by-grade/{gradeLevel}`, publik tanpa auth).
2. Pilih **nama murid** dari dropdown, lalu masukkan **PIN 4 digit**.
3. Submit ke `POST /learner-login` (`LearnerLoginController@login`) — PIN dicocokkan langsung (`$learner->pin === $request->pin`, **plaintext, tidak di-hash**).
4. Kalau cocok: disimpan di **session key `learner_id`** (bukan `Auth::login()` Laravel biasa, tidak pakai guard apapun) → redirect ke `/learner/dashboard`.

Akses halaman Murid (`/learner/dashboard`, `/learner/tugas/*`, `/learner/raport`) dijaga middleware baru **`auth.learner`** (`LearnerAuth`), yang cuma mengecek `session('learner_id')` valid — **bukan** middleware `auth` Laravel bawaan. Logout murid: `POST /learner-logout` (hapus session `learner_id`).

Murid sekarang pakai layout baru **`layouts.learner`** (bukan `layouts.app` lagi), dengan tampilan topbar/sidebar sendiri. Variabel `$learner` disuntik otomatis ke semua view yang extend layout ini lewat View Composer di `AppServiceProvider`.

⚠️ **Catatan keamanan** (belum diperbaiki, sekadar dicatat): PIN disimpan & dicocokkan sebagai plaintext (tidak di-hash), dan tidak ada rate-limiting/lockout untuk percobaan PIN salah berulang — berpotensi rawan brute-force kalau PIN cuma 4 digit. Perlu jadi perhatian ke depan.

**Cara membuat akun berbeda per role:**
- **Murid**: **tidak bisa** self-register lagi via `/register` (karena sekarang butuh PIN, bukan email/password) — akun murid dibuat oleh Admin lewat halaman **Murid** (`admin.learners.index`), diisi `nama_lengkap`, `grade_level`, `section`, dan `pin` (email sekarang opsional).
- **Guru & Admin**: **tidak bisa** membuat akun sendiri. Akunnya hanya bisa dibuat oleh Admin lewat menu "Daftarkan Pengguna" (`/register-user`), yang punya pilihan role Admin/Guru/Murid (role Murid dari form ini kemungkinan sudah tidak relevan lagi karena murid tidak login pakai email/password — perlu dicek ulang apakah form ini masih dipakai untuk Murid).
- Begitu akun Admin/Guru dibuat, pemiliknya bisa login sendiri kapan saja tanpa perlu bantuan admin lagi. Murid butuh **PIN yang di-set oleh Admin** untuk bisa login sendiri.

Role (Admin/Guru/Learner) disimpan menggunakan package **Spatie Laravel Permission** — ini tetap dipakai untuk User model (Admin/Guru), sedangkan Murid **tidak** pakai sistem role ini sama sekali (murid bukan `User`, melainkan model `Learner` terpisah dengan sistem auth sendiri). Role awal dibuat lewat `RoleSeeder`, yang **aman dijalankan berulang** (idempotent):

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

### 4.12 Kelola Tingkat Kelas & Tahun Ajaran (CRUD, ganti dari hardcode)
- Pilihan "Tingkat Kelas" (1st Year, dst) dan "Kelompok/Tahun Ajaran" (A/B/C/D) di form Murid sebelumnya di-hardcode di kode, tidak sesuai kondisi kelas Al-Barokah sebenarnya.
- Dibuat tabel baru `grade_levels` dan `sections` + model `GradeLevel`/`Section`, halaman admin baru **"Kelas & Tahun Ajaran"** (`ClassSettingController`, route `admin.class-settings.*`, `admin.grade-levels.*`, `admin.sections.*`) untuk tambah/edit/hapus pilihan sendiri.
- Form Tambah/Edit Murid sekarang menarik pilihan dropdown secara dinamis dari kedua tabel ini (validasi `exists:grade_levels,name` / `exists:sections,name`).
- `GradeLevelSectionSeeder` mengisi data default (nilai lama) supaya form tidak kosong sebelum admin menyesuaikan.
- Label "Kelompok" kemudian diganti jadi **"Tahun Ajaran"** di seluruh tampilan (hanya rename label, struktur tabel `sections`/kolom `section` tidak berubah).

### 4.13 (Di luar sesi ini) Fitur Tugas/Assignment, Rapor, dan Login Murid via PIN
Ditemukan lewat `git pull` — perubahan besar berikut **dikembangkan di luar sesi pendampingan ini**, sudah live di server produksi:

**a) Perubahan struktur tabel `learners`** (3 migration berurutan, 24 Juli 2026):
- `email` diubah jadi **nullable** (sebelumnya wajib).
- Kolom baru `pin` (string, nullable) ditambahkan untuk login murid.
- Kolom `fname`, `mname`, `lname` **digabung jadi satu kolom `nama_lengkap`** (data lama otomatis digabung lewat migration, spasi ganda dirapikan). Rollback (`down()`) tidak bisa mengembalikan pemisahan nama karena datanya sudah digabung.
- `$fillable` di `Learner.php` sekarang: `['nama_lengkap', 'email', 'pin', 'grade_level', 'section']`.

**b) Login Murid via PIN** — lihat detail lengkap di bagian 3 di atas. Ringkas: terpisah total dari `Auth::user()` Laravel, pakai session key `learner_id` manual + middleware `auth.learner`.

**c) Sistem Tugas/Assignment**:
- Admin buat tugas (`Assignment`: judul, deskripsi, deadline, target `kelas` atau `individu`) lewat halaman `admin/assignments` (`AssignmentController`, resource route `admin.assignments.*`).
- Admin tambah soal per tugas (`AssignmentQuestion`: tipe `pilgan`/`essay`, opsi JSON, kunci jawaban, poin) via `AssignmentQuestionController`.
- Admin assign tugas ke murid (satu kelas sekaligus, atau pilih individu) → tercatat di tabel pivot `assignment_learners` (status `belum`/`selesai`, waktu submit, total skor).
- Murid buka tugas yang ditugaskan ke dirinya (`/learner/tugas`), kerjakan, submit. Soal **pilgan otomatis dikoreksi** saat submit; soal **essay dinilai manual oleh admin** lewat halaman "Lihat Jawaban Murid" (`admin.assignments.learner-answers` + `.grade`).
- Murid tidak bisa mengerjakan tugas yang tidak ditugaskan ke dirinya, submit dua kali, atau submit setelah lewat deadline (dicegah di controller).

**d) Rapor Murid**:
- Dihitung murni dari **rata-rata persentase skor tugas yang sudah selesai** (bukan dari data kehadiran). Predikat: ≥90 "Sangat Baik", ≥75 "Baik", ≥60 "Cukup", di bawahnya "Perlu Perbaikan".
- Admin bisa lihat rapor semua murid (`admin/raport`) atau detail satu murid (`admin/raport/{learner}`). Murid bisa lihat rapor miliknya sendiri (`/learner/raport`).
- ⚠️ Logika hitung rata-rata & predikat **terduplikasi** di `Admin\RaportController` dan `Learner\AssignmentController` (method private yang sama persis di dua tempat) — kandidat refactor ke trait/service kalau ingin dirapikan, tapi bukan bug fungsional.

**e) Perubahan pendukung lain**: middleware baru `auth.learner` didaftarkan di `bootstrap/app.php`; View Composer baru di `AppServiceProvider` untuk suntik variabel `$learner` ke semua view `layouts.learner`.

### 4.14 Tindak lanjut catatan keamanan & kualitas dari bagian 4.13
Tiga catatan yang diangkat di dokumentasi (bagian 6) sudah ditindaklanjuti:

1. **PIN murid di-hash** — `LearnerController::store()`/`update()` sekarang menyimpan PIN dengan `Hash::make()` (bcrypt), dan `LearnerLoginController::login()` mencocokkan pakai `Hash::check()`, bukan perbandingan plaintext. Migration baru `hash_plaintext_pins_on_learners_table` meng-hash ulang PIN lama yang masih plaintext di database produksi (deteksi otomatis: string < 60 karakter dianggap plaintext, aman dijalankan berulang).
2. **Rate-limiting login PIN** — ditambahkan `RateLimiter` per kombinasi (murid + IP): maksimal 5 percobaan gagal per menit sebelum diblokir sementara, plus `throttle:20,1` di level route sebagai lapisan tambahan. Mencegah brute-force PIN 4 digit.
3. **Opsi role "Murid" dihapus dari form Register User** — karena murid sekarang dibuat & login lewat mekanisme PIN terpisah (halaman Murid), bukan lagi lewat form ini. Validasi role di `RegisterController` disesuaikan jadi `admin,guru` saja.
4. **Duplikasi logika rapor dirapikan** — method `hitungRataRataPersen()` dan `hitungPredikat()` yang sebelumnya diduplikasi di 3 controller (`Admin\RaportController`, `Learner\AssignmentController`, `LearnerController`) sekarang jadi satu trait `App\Support\CalculatesRaport`, dipakai bersama oleh ketiganya.

Catatan: route publik self-register (`/register`, `RegisterController::register()`) yang otomatis assign role `learner` ke `User` **tidak diubah** karena sudah tidak ditautkan di UI manapun (link-nya sudah di-comment di `welcome.blade.php` sejak awal) — dibiarkan idle, bukan prioritas untuk dibersihkan sekarang.

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
- ~~PIN login Murid disimpan plaintext, tanpa rate-limiting~~ — **sudah diperbaiki**, lihat 4.14 (PIN di-hash bcrypt + rate-limiting).
- ~~Opsi role "Murid" di form Register User~~ — **sudah dihapus**, lihat 4.14.
- ~~Duplikasi logika hitung rapor~~ — **sudah dirapikan** jadi trait `App\Support\CalculatesRaport`, lihat 4.14.
- Route publik self-register (`/register`) yang assign role `learner` ke `User` masih ada tapi **tidak ditautkan di UI manapun** (dead code, aman dibiarkan idle) — lihat catatan di 4.14.
