# Catatan Perubahan

Ringkasan pekerjaan yang dilakukan, ditulis berurutan dari yang paling baru.
Tujuannya supaya mudah dibaca tanpa harus menelusuri riwayat commit satu per satu.

---

## 31 Juli 2026 — Tahap 1: Kerangka tampilan responsif (sidebar HP)

**Masalah yang diperbaiki**

Aplikasi sebelumnya hanya nyaman dibuka di desktop. Dua akar masalahnya:

1. Layout **Admin** dan **Murid** tidak punya tag `<meta name="viewport">`. Tanpa tag ini,
   HP merender halaman selebar layar desktop (± 980px) lalu memperkecilnya — inilah
   penyebab utama tulisan jadi kecil-kecil dan harus dicubit-zoom.
2. Sidebar dipasang mati selebar 200px. Di layar HP (± 375px) sidebar memakan lebih dari
   separuh layar, dan tidak ada satu pun aturan responsif (`@media`) di seluruh layout.

**Yang dikerjakan**

- Menambahkan tag `viewport` di layout Admin dan Murid (layout Guru sudah punya).
- Sidebar sekarang **berubah jadi laci geser (off-canvas)** di layar di bawah 992px:
  tersembunyi secara default, muncul menggeser dari kiri saat tombol menu ditekan,
  dengan latar gelap yang bisa diketuk untuk menutup. Bisa juga ditutup dengan tombol
  `Esc`, dan menutup sendiri setelah salah satu menu dipilih.
- Di desktop perilakunya **tidak berubah** — tombol menu tetap menyempitkan sidebar
  jadi rail ikon seperti sebelumnya.
- Jarak (padding) konten dan ukuran judul topbar ikut mengecil di layar HP supaya
  ruang layar tidak habis dipakai bingkai.

**Perapian struktur (penting untuk perawatan ke depan)**

CSS kerangka tampilan sebelumnya **disalin tiga kali** di dalam `<style>` masing-masing
layout (Admin, Guru, Murid), begitu juga fungsi JavaScript-nya. Artinya setiap perbaikan
harus ditulis ulang tiga kali dan rawan terlewat di salah satunya. Sekarang dijadikan
satu file bersama:

| Berkas baru | Isi |
|---|---|
| `public/css/app-shell.css` | Seluruh gaya sidebar, topbar, konten, plus aturan responsif HP |
| `public/js/app-shell.js` | Perilaku buka/tutup sidebar (sadar desktop vs HP) & laci notifikasi |

Ketiga layout kini cuma memanggil dua berkas di atas. Total baris ketiga layout turun
dari **1.467 menjadi 677 baris** tanpa mengurangi fitur apa pun.

**Berkas yang disentuh**

- `public/css/app-shell.css` (baru)
- `public/js/app-shell.js` (baru)
- `resources/views/layouts/guru.blade.php`
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/learner.blade.php`

**Catatan efek samping yang disengaja**

Aturan tampilan kartu (`.card` — tanpa garis tepi, sudut membulat, bayangan halus) dulu
hanya ada di layout Guru. Karena sekarang satu berkas dipakai bersama, tampilan kartu di
halaman Admin dan Murid ikut menyesuaikan supaya seragam. Ini perubahan tampilan saja,
tidak memengaruhi fungsi.

**Yang perlu dicoba**

Buka lewat HP (atau kecilkan jendela browser sampai di bawah 992px) untuk peran
Admin, Guru, dan Murid:

- Sidebar harus tersembunyi, dan muncul sebagai laci saat tombol menu (☰) ditekan.
- Mengetuk area gelap di sebelah laci harus menutup laci tersebut.
- Memilih salah satu menu harus menutup laci dan langsung membuka halaman tujuan.
- Di layar desktop, tombol menu harus tetap berperilaku seperti biasa (menyempit jadi ikon).

**Belum dikerjakan (menyusul di tahap berikutnya)**

- Halaman **Latihan Mengetik**: ukuran huruf teks latihan dan kotak statistik hasil
  (4 kolom) masih terasa sesak di layar HP.
- Halaman **Raport Admin**: tabel sudah bisa digeser ke samping, tapi masih perlu banyak
  geser jempol; sebaiknya kolom yang kurang penting disembunyikan di layar kecil.
- Halaman **Kuis**: ukuran huruf soal masih agak besar untuk HP.
- Halaman **Login**: sudah responsif, tinggal perapian kecil (ukuran area sentuh tombol).

---

## 31 Juli 2026 — Kuis & Latihan Mengetik jadi milik masing-masing guru

**Latar belakang**

Permintaan kepala sekolah: setiap guru punya ujian/tugasnya sendiri. Sebelumnya fitur
Kuis dan Latihan Mengetik bersifat global — semua guru melihat dan mengelola data yang
sama persis, padahal praktiknya hanya dipakai guru TIK.

**Yang dikerjakan**

- Tabel baru `subject_teacher` untuk mencatat **mata pelajaran yang diampu tiap guru**.
  Diatur Admin lewat halaman **Pengguna** → tombol edit pada guru → centang mapelnya.
- Tabel `quiz_levels` dan `typing_levels` kini punya kolom pemilik (`guru_id` + `subject_id`).
  Nomor tahap sekarang unik **per guru**, bukan global — jadi dua guru boleh sama-sama
  punya "Tahap 1" tanpa bentrok.
- **Kuis Pilihan Ganda** dibuka untuk **semua guru**. Setiap guru hanya bisa melihat dan
  mengelola kuis pada mapel yang diampunya (muncul tab pemilih mapel bila mengampu lebih
  dari satu). Akses ke kuis milik guru lain ditolak, termasuk bila URL-nya dibuka langsung.
- **Latihan Mengetik** tetap **khusus guru TIK**. Menunya disembunyikan dari guru lain
  (di sidebar maupun di kartu Aksi Cepat dasbor), dan aksesnya juga ditolak dari sisi
  server, bukan sekadar disembunyikan tampilannya.
- **Tampilan murid**: menu Kuis sekarang menampilkan **daftar mata pelajaran lebih dulu**,
  baru tahap-tahapnya. Perhitungan progres (lulus / tahap terkunci / "Mode Pamungkas")
  dihitung terpisah per mapel, tidak lagi tercampur antar guru.

**Perpindahan data lama**

Seluruh tahap Kuis dan Latihan Mengetik yang sudah ada dipindahkan otomatis ke akun
guru TIK (`muzadidilfuad@gmail.com`) dengan mapel `TIK` — dibuat otomatis bila belum ada.
Guru lain mulai dari kosong. Ini berjalan otomatis lewat migrasi, tidak perlu tindakan manual.

**Perbaikan menyusul**

- Tombol "Master Latihan Mengetik" pada kartu Aksi Cepat di dasbor guru sempat masih
  tampil untuk semua guru — sudah diperbaiki.
- Halaman Latihan Mengetik sempat error `500`. Penyebabnya: pada Laravel 12, kelas
  `Controller` bawaan sudah tidak lagi menyediakan method `middleware()`, sehingga
  pemanggilannya di constructor menyebabkan gagal total. Pengecekan hak akses dipindahkan
  ke masing-masing method.

**Perintah yang perlu dijalankan setelah menarik perubahan ini**

```
php artisan migrate
```
