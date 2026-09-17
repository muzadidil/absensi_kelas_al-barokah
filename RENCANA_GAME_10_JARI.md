# Rencana: Game 10 Jari (Menu Murid)

> **Status:** Konsep inti **JILID 1–5 sudah disepakati** (lihat §3). JILID 6 dst menyusul
> setelah JILID 1–5 jalan (lihat §9). **Belum dikerjakan** — dokumen ini acuan sebelum mulai
> bangun, bisa dilanjut diskusi di sesi lain.
> Dibuat: 2026-09-17. Diperbarui: 2026-09-17 (sesi ke-2).

## 1. Latar Belakang

Sudah ada menu **"Latihan Mengetik 10 Jari"** (`learner.typing.*`) — tahap berjenjang, tiap
tahap punya `allowed_keys` + `word_bank`, dinilai dari **WPM, akurasi, % salah**. Ini bagus
untuk kecepatan & akurasi ketik kata, tapi **tidak ada unsur "game"** yang memancing murid
mengulang untuk seru-seruan, dan tidak spesifik melatih *muscle memory* posisi jari di
*home row*. **Game 10 Jari** ini melengkapi itu — nuansa arcade, personal, bertema lokal
(santri Pondok Al-Barokah), terinspirasi game ketik klasik **Typer Shark!** (PopCap) tapi
dengan tema & mekanik sendiri.

## 2. Tujuan

- Melatih posisi jari di *home row* (`A S D F G H J K L ;` — pas 10 tombol, delapan jari inti
  + kelingking kanan di `;`) lewat repetisi yang terasa seperti main game, bukan ujian.
- Personal & related ke identitas murid (nama sendiri jadi tokoh utama, nama sekolah jadi
  taruhan cerita) supaya lebih nempel emosional dibanding game generik.
- Melengkapi Latihan Mengetik, **bukan menggantikan** — menu terpisah, tidak memengaruhi
  syarat lulus Latihan Mengetik yang sudah ada.

## 3. Konsep yang Disepakati — JILID 1–5

### 3.1 Tema & Tokoh
- **Tokoh utama = nama siswa yang sedang login** (ditampilkan sebagai identitas pemain di
  layar, bukan avatar generik).
- **Musuh = Meteor** (bukan ikan seperti Typer Shark) — jatuh dari atas layar.
- **Narasi**: siswa menyelamatkan **Al-Barokah** dari serangan meteor.

### 3.2 Intro / Pembuka
Sebelum meteor mulai jatuh, tampilkan animasi teks pembuka yang dipersonalisasi:

> "Selamat datang **[NAMA SISWA]**, apakah kamu siap menyelamatkan Al-Barokah dari Meteor?
> Tekan **SPASI** jika siap."

Tekan Spasi → game dimulai.

### 3.3 Gameplay Inti (gelombang biasa, sebelum boss)
- Meteor jatuh dari atas, tiap meteor bawa **satu huruf tunggal** (bukan kata di fase ini).
- Huruf dibatasi ke **home row saja**: `A S D F G H J K L ;`.
- **Kecepatan jatuh meteor konstan/standar** di sepanjang JILID 1–5 — **tidak dipercepat**.
- Kesulitan naik lewat **jumlah/frekuensi meteor yang muncul** tiap JILID (makin ke JILID
  atas, makin banyak/padat meteornya), bukan lewat kecepatan jatuh.
- Murid tekan tombol fisik yang sesuai untuk menghancurkan meteor sebelum mendarat.

### 3.4 Nyawa & Syarat Lulus
- **5 nyawa per JILID** (nyawa fresh tiap mulai/ulang satu JILID, dipakai selama gelombang
  biasa + lawan boss di ujungnya — satu rangkaian).
- Meteor yang tidak sempat diketik sampai dasar → **kurangi 1 nyawa**.
- **Tolok ukur lulus JILID = berhasil mengalahkan boss di ujung JILID itu** sebelum nyawa
  habis. Habis nyawa sebelum boss kalah → JILID itu gagal.

### 3.5 Boss per JILID (istilah "fase" → **JILID**, biar familiar buat santri)

| JILID | Boss | Senjata Boss | Peluru (huruf) / Serangan | Checkpoint? |
|---|---|---|---|---|
| 1 | Pocong | Bola Api | 1 | Tidak |
| 2 | Wewe Gombel | Bola Es Salju | 2 | Tidak |
| 3 | Genderuwo | Rumput Bulat | 3 | Tidak |
| 4 | Kelelawar | Anak Kelelawar | 4 | Tidak |
| 5 | Orang naik UFO | Tembakan UFO | 4 | **Ya** |

Tiap serangan boss memuntahkan sejumlah huruf (home row) sekaligus sesuai kolom di atas —
makin ke JILID atas, makin banyak huruf per serangan (konsisten dgn prinsip "jumlah, bukan
kecepatan" di §3.3). *(Catatan: JILID 4 & 5 sama-sama 4 peluru/serangan — diasumsikan
disengaja, karena JILID 5 nilainya sebagai boss checkpoint bukan dari nambah peluru lagi.
Tinggal koreksi kalau ternyata JILID 5 mestinya lebih banyak.)*

### 3.6 Checkpoint di JILID 5
- **Sebelum JILID 5** (JILID 1–4): kalau gagal (nyawa habis sebelum boss kalah) → **ulang
  dari JILID 1 lagi** di kesempatan berikutnya.
- **JILID 5 = checkpoint**: begitu boss JILID 5 berhasil dikalahkan, checkpoint tersimpan.
  Kalau setelah itu gagal lagi (mis. nanti di JILID 6+), **tidak lagi balik ke JILID 1** —
  cukup ulang dari JILID checkpoint terakhir yang sudah ditembus.
- *(Teknis: ini pola yang sama persis dengan "Mode Pamungkas" `reset_to_first_on_fail` yang
  sudah ada di Kuis Pilihan Ganda — tinggal reuse konsepnya: tandai `is_checkpoint` pada
  JILID tertentu, titik mulai ulang murid = checkpoint tertinggi yang sudah lolos, +1.)*

### 3.7 WPM — Tolok Ukur, Bukan Syarat Lulus
- Target akhir arc JILID 1–5: siswa idealnya bisa **~25 WPM**.
- **Ini bukan syarat lulus** tiap JILID/boss — murni tolok ukur yang ditampilkan ke siswa/guru.
- Konteks: target awal sempat dipasang **50 WPM**, ternyata siswa masih kesulitan mencapainya,
  jadi tolok ukur diturunkan ke 25 untuk arc ini. Kecepatan target akan **dinaikkan lagi di
  JILID atas** (JILID 6+) seiring jam terbang siswa bertambah.

### 3.8 Ruang Lingkup Sekarang
- **Fokus: JILID 1–5 sampai berfungsi penuh** (intro, gelombang meteor, nyawa, boss per
  JILID sesuai §3.5, checkpoint di JILID 5, catatan WPM).
- **JILID 6 dst dirancang & dibangun belakangan**, menyusul setelah JILID 1–5 jalan.

## 4. Rancangan Data (draf teknis)

Meniru pola `typing_levels` / `typing_attempts` + reuse ide `reset_to_first_on_fail` dari Kuis:

- **`meteor_game_levels`** (JILID):
  - `level_number` (1, 2, 3, …), `display_label` (mis. "JILID 1")
  - `allowed_keys` (default home row `asdfghjkl;`, biar fleksibel buat JILID 6+ nanti)
  - `lives` (default 5)
  - `boss_name`, `boss_weapon_name`, `boss_bullets_per_shot`
  - `is_checkpoint` (boolean — `true` untuk JILID 5)
- **`meteor_game_attempts`**:
  - `learner_id`, `meteor_game_level_id`, `passed` (boolean — lulus = boss kalah), `wpm`,
    `created_at`
  - Dipakai untuk: JILID mana yang terbuka/harus diulang, & checkpoint tertinggi yang sudah
    ditembus per murid (`max level_number` dari attempt `passed=true AND is_checkpoint=true`).

*(Sengaja tabel baru, bukan reuse `typing_levels`, karena mekanik penilaiannya beda jauh:
nyawa/boss/checkpoint vs WPM-akurasi-lulus tegas.)*

## 5. Raport / Progres

Kemungkinan cukup tampil di **dashboard murid** (JILID tercapai + WPM terakhir), belum tentu
masuk ke Raport akademik formal (`learner.raport`) — ini masih perlu dikonfirmasi (lihat §7).

## 6. Dampak ke Menu & Navigasi (setelah siap dibangun)

- Sidebar murid (`resources/views/layouts/learner.blade.php`): item baru, ikon mis.
  `bi-controller` atau `bi-rocket-takeoff` (tema meteor/luar angkasa).
- Dashboard murid (`resources/views/learner/dashboard.blade.php`): kartu ke-3 sejajar Kuis &
  Latihan Mengetik.
- Route baru di bawah middleware `auth.learner`, pola sama seperti Typing:
  `GET /learner/game-jari`, `GET /learner/game-jari/{level}`, `POST .../attempt`.

## 7. Pertanyaan Terbuka (sisa, sebelum mulai bangun)

1. **Per-guru/mapel atau global?** Typing & Kuis sekarang per-guru-per-mapel. Game 10 Jari
   ceritanya (Pocong, Wewe Gombel, dst) sepertinya pas **satu set global bawaan sistem**
   (tidak per guru) — asumsi ini benar, atau tetap mau bisa diatur guru?
2. **Device target** — wajib keyboard fisik (lab komputer), atau perlu jalan juga di HP/
   tablet? (Karena intinya latihan jari di keyboard fisik, mungkin cukup blokir/kasih pesan
   kalau diakses dari HP.)
3. **Raport** (§5) — cukup dashboard murid saja, atau perlu kelihatan di raport guru/admin
   juga untuk pantau progres?
4. **Visual meteor & boss** — pakai ilustrasi/sprite sederhana (SVG/CSS) atau ada aset
   gambar yang mau dipakai (mis. sudah ada gambar Pocong/Wewe Gombel dsb yang mau dipasang)?

## 8. Roadmap Kasar

- **Fase 0** — Sepakati sisa §7, finalisasi struktur data (§4).
- **Fase 1** — Intro personalisasi + arena meteor huruf tunggal (home row) + nyawa 5 + render
  keyboard/feedback dasar.
- **Fase 2** — Boss per JILID (1–5) sesuai §3.5, deteksi menang/kalah, logika checkpoint §3.6.
- **Fase 3** — Rekor per murid (JILID tercapai, WPM terakhir) + tampilan dashboard.
- **Fase 4 (nanti)** — JILID 6 dst (kecepatan mulai naik, target WPM naik dari 25 → lebih
  tinggi bertahap, kemungkinan mulai masuk baris atas/bawah keyboard).

## 9. Prinsip Reuse

- Pola tahap berjenjang + kunci progresif (dari Typing & Kuis).
- Pola **checkpoint / reset-ke-awal** — reuse ide `reset_to_first_on_fail` dari Kuis Pilihan
  Ganda, dibalik defaultnya (JILID 1–4 default reset ke 1, JILID 5 mematikan reset itu).
- Pola "attempt" tersimpan per percobaan → dipakai untuk rekor & buka JILID berikutnya.
- Layout kartu tahap murid & style sidebar/dashboard yang sudah konsisten di app ini.
