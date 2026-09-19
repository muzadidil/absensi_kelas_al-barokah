# Catatan Perubahan

Ringkasan pekerjaan yang dilakukan, ditulis berurutan dari yang paling baru.
Tujuannya supaya mudah dibaca tanpa harus menelusuri riwayat commit satu per satu.

---

## 19 September 2026 — SPASI untuk lanjut ke JILID berikutnya

**Yang dikerjakan**

Setelah boss tumbang, tangan masih di keyboard tapi untuk lanjut ke JILID berikutnya harus
meraih tetikus. Sekarang cukup tekan **SPASI**.

- Layar **menang** → SPASI langsung membuka JILID berikutnya. Kalau sudah JILID terakhir,
  SPASI tidak melakukan apa-apa (tombol Ulangi dan Daftar JILID tetap bisa diklik).
- Layar **kalah** → SPASI mengulang JILID yang sama, sama seperti tombol "Coba Lagi".
- Tombolnya tetap ada seperti biasa, jadi di HP pun tidak ada yang berubah.

**Penjaga supaya tidak kepencet**

Saat boss tumbang, biasanya murid masih menekan-nekan tombol. Kalau SPASI langsung aktif,
layar hasil bisa terlewat begitu saja tanpa sempat dibaca. Karena itu SPASI baru berfungsi
**0,6 detik setelah** layar hasil muncul. Tombolnya sendiri bisa diklik kapan saja.

**Berkas yang disentuh**

- `resources/views/learner/meteor/play.blade.php`

Tidak ada perubahan database. Deploy cukup `git pull` lalu `php artisan optimize:clear`.

**Yang perlu dicoba**

- Menangkan satu JILID → coba tekan SPASI secepatnya → harus **tidak** langsung lompat.
- Tunggu sebentar → tekan SPASI → harus pindah ke JILID berikutnya.
- Kalah di satu JILID → tekan SPASI → JILID yang sama harus dimulai ulang dengan nyawa penuh.

---

## 19 September 2026 — Game 10 Jari bisa dimulai tanpa keyboard

**Masalah yang diperbaiki**

Satu-satunya cara memulai permainan dan melanjutkan dari jeda adalah menekan **SPASI**. Di HP
tidak ada tombol SPASI, jadi permainannya sama sekali tidak bisa dibuka untuk dicoba.

**Yang dikerjakan**

- Tombol **Mulai** di layar pembuka dan tombol **Lanjut** di layar jeda. Tulisan
  "atau tekan SPASI" tetap ada, jadi keduanya bisa dipakai — mana saja yang lebih enak.
- Di HP, **menyentuh arena akan memunculkan keyboard layar**, sehingga hurufnya bisa diketik
  untuk sekadar mencoba. Caranya: tekan Mulai, lalu sentuh arenanya.
- Keterangan peringatan di atas arena diperbarui: game ini tetap dirancang untuk keyboard
  fisik (latihan 10 jari memang tidak bisa dilatih di layar sentuh), tapi sekarang dijelaskan
  cara mencobanya di HP.

**Catatan teknis**

Satu tombol fisik bisa menyalakan dua kejadian sekaligus (tombol ditekan **dan** teks masuk),
terutama di tablet yang punya keyboard. Supaya satu ketikan tidak terhitung dua kali, ketikan
yang sama dalam waktu sangat berdekatan hanya diproses sekali.

**Berkas yang disentuh**

- `resources/views/learner/meteor/play.blade.php`

Tidak ada perubahan database. Deploy cukup `git pull` lalu `php artisan optimize:clear`.

**Yang perlu dicoba**

- Buka di HP → tekan **Mulai** → permainan harus jalan tanpa perlu keyboard.
- Sentuh arenanya → keyboard layar harus muncul, dan huruf yang diketik menghancurkan meteor.
- Jeda → tekan tombol **Lanjut** → permainan lanjut.
- Di komputer, **SPASI** harus tetap berfungsi seperti biasa untuk mulai dan lanjut.

---

## 19 September 2026 — Game 10 Jari: tombol Layar Penuh

**Masalah yang diperbaiki**

Arena permainan tingginya dibatasi 560 piksel, jadi di layar lebar terlihat kecil dan seperti
terpotong. Tidak ada cara memperbesarnya, dan menekan F11 di browser pun tidak membantu karena
ukuran arenanya tetap dihitung dari lebar kartu, bukan dari layar.

**Yang dikerjakan**

- Tombol **Layar Penuh** di samping tombol Jeda. Ditekan sekali, arena memenuhi seluruh layar;
  ditekan lagi (atau tekan `Esc`) kembali normal.
- Saat layar penuh, deretan informasi (nyawa, darah boss, kombo, akurasi, WPM, waktu)
  **ikut pindah ke dalam arena** dan mengambang di atas, supaya tetap kelihatan.
- Tebal tanah sekarang mengikuti tinggi arena, tidak lagi dipatok 56 piksel, agar tidak
  terlihat setipis garis di layar besar.
- Masjid, boss, dan huruf pada meteor ikut membesar di arena besar supaya tetap enak dibaca.

**Perbaikan sampingan**

Tombol yang baru saja diklik tetap dalam keadaan terpilih, sehingga saat pemain menekan
**SPASI** untuk melanjutkan permainan, tombol itu ikut tertekan dan permainan langsung terjeda
lagi. Sekarang fokusnya dilepas setiap kali tombol diklik.

**Berkas yang disentuh**

- `resources/views/learner/meteor/play.blade.php`

Tidak ada perubahan database. Deploy cukup `git pull` lalu `php artisan optimize:clear`.

**Yang perlu dicoba**

- Tekan **Layar Penuh** saat bermain → arena harus memenuhi layar dan informasi nyawa tetap terlihat.
- Tekan `Esc` → keluar dari layar penuh dan tampilan kembali seperti semula.
- Jeda permainan, lalu tekan **SPASI** → harus langsung lanjut, tidak terjeda lagi seketika.

---

## 19 September 2026 — Game 10 Jari bisa diatur sendiri dari Admin

**Masalah yang diperbaiki**

JILID 6 dan 7 ternyata terlalu berat — bahkan untuk yang sudah terbiasa mengetik 10 jari.
Penyebabnya, di JILID 6 ada **tiga hal yang naik sekaligus**: huruf baru bertambah satu baris
penuh, peluru boss melonjak jadi 5 butir, dan meteor turun makin rapat. Ketiganya menumpuk.

Masalah yang lebih besar: semua angka kesulitan itu **terkunci di dalam kode**. Setiap kali
terlalu berat atau terlalu ringan, harus menunggu programmer mengubahnya. Padahal yang paling
tahu kemampuan murid adalah gurunya sendiri.

**Yang dikerjakan**

Sekarang ada menu **Game 10 Jari** di sidebar Admin. Semua bisa diatur sendiri lewat lima tab:

| Tab | Isinya |
|---|---|
| **JILID** | Urutan, huruf yang keluar, nyawa, jumlah meteor, jeda antar meteor, **lama meteor jatuh**, boss & pelurunya, darah boss, jumlah peluru per serangan, jeda serangan, centang "peluru memantul" dan "checkpoint" |
| **Nuansa** | Warna langit (3 lapis), tanah, dinding & kubah markas, cahaya perisai, objek langit (bintang/awan/polos), dan apakah latarnya gelap |
| **Efek** | Jumlah & sebaran partikel ledakan, warna partikel, kekuatan getaran layar, warna sinar tembakan |
| **Peluru** | Nama (sekaligus jadi nama senjata boss), tiga warna bolanya, dan efek saat kena |
| **Boss** | Nama bebas, pilih gambarnya dari 10 yang tersedia, dan warna auranya |

Nuansa, Efek, Peluru, dan Boss adalah **master** — dibuat sekali, boleh dipakai berkali-kali di
JILID mana pun. Jadi kalau mau bikin JILID baru bertema Senja dengan boss Mecha dan peluru es,
tinggal pilih dari daftar, tidak perlu bikin ulang.

**Kecepatan sekarang bisa diatur**

Dulu lama meteor jatuh terkunci 7 detik di dalam kode. Sekarang ada isiannya per JILID
(**makin besar angkanya, makin pelan meteornya**), begitu juga lama peluru boss jatuh dan jeda
antar serangan boss.

**Keseimbangan bawaan dilandaikan**

Selain bisa diatur, angka bawaannya juga diperbaiki. Prinsipnya sekarang: **kalau huruf baru
diperkenalkan, tekanan lain justru diturunkan** — biar yang sulit cuma satu hal pada satu waktu.

- Huruf melebar **dua-dua**, bukan langsung satu baris. JILID 6 cuma menambah **E** dan **I**
  (jari telunjuk naik), bukan `Q W E R T` sekaligus.
- Di JILID 6 jumlah peluru boss justru **turun** dari 5 jadi 2, dan meteornya dibuat lebih renggang.
- JILID 6 dan 7 meteornya dibuat lebih lambat (7,5 detik), supaya ada waktu mencari huruf baru.

Kalau ternyata masih terlalu berat atau malah terlalu gampang, semuanya bisa langsung diubah
dari halaman Admin tanpa menyentuh kode lagi.

**Berkas yang disentuh**

- 2 migrasi baru: 4 tabel master + penyambungan JILID ke master & kolom kecepatan
- Model baru `MeteorTheme`, `MeteorEffect`, `MeteorBullet`, `MeteorBoss`
- `MeteorGameLevel` — semua pengaturan dirakit di satu method `gameConfig()`
- `Admin\MeteorGameController` + view `admin/meteor/index.blade.php` (baru)
- `learner/meteor/play.blade.php` — berhenti menebak dari nomor JILID, semua dari data
- `routes/web.php`, `layouts/admin.blade.php`, `MeteorGameLevelSeeder`

**Perintah yang perlu dijalankan setelah menarik perubahan ini**

```
php artisan migrate --force
php artisan db:seed --class=MeteorGameLevelSeeder --force
php artisan optimize:clear
```

Migrasinya **memindahkan data JILID yang sudah ada** ke bentuk baru, jadi JILID 1–10 di server
tidak akan hilang. Seeder tetap perlu dijalankan untuk mengisi daftar Nuansa, Efek, Peluru, dan
Boss, sekaligus menerapkan keseimbangan baru.

**Yang perlu dicoba**

- Buka **Admin → Game 10 Jari** → harus muncul 5 tab dengan data terisi.
- Ubah satu JILID (misal huruf atau lama jatuhnya) → simpan → mainkan sebagai murid, perubahannya
  harus langsung terasa.
- Buat **Nuansa** baru dengan warna bebas, pasang ke satu JILID → langit dan markasnya harus berubah.
- Buat **Boss** baru bernama apa saja dengan gambar pilihan sendiri → pasang ke JILID.
- Hapus satu master yang sedang dipakai → JILID-nya **tidak ikut terhapus**, hanya kembali ke
  tampilan bawaan.
- Coba isi huruf dengan yang dobel (misal `aabbcc`) → harus otomatis dirapikan jadi `abc`.

**Catatan pengujian**

Diuji di Chrome tanpa jendela: **27/27** untuk permainannya setelah dirombak (termasuk bukti
bahwa nuansa "Senja" yang tidak pernah ada di kode bisa tampil hanya dari data), dan **27/27**
untuk logika form di halaman admin (tambah vs ubah, isian tidak bersisa saat ganti mode,
konfirmasi hapus).

Seperti biasa, **sisi PHP/Laravel belum dijalankan** di komputer pengerjaan karena PHP tidak
terpasang — migrasi, controller, dan halaman admin perlu dicoba sekali di server.

**Belum dikerjakan**

- Gambar boss masih dipilih dari 10 yang sudah ada; menggambar boss baru lewat form belum bisa.
- Progres game belum masuk Raport.

---

## 18 September 2026 — Game 10 Jari: JILID 6–10 (suasana pagi, boss modern, peluru memantul)

**Yang dikerjakan**

Lanjutan dari JILID 1–5. Sekarang ada 10 JILID. Tiga hal baru di babak kedua:

**1. Suasana pagi.** JILID 6–10 tidak lagi malam. Langitnya biru dengan matahari rendah di
ufuk, awan berjalan pelan, rumput hijau, dan markas Al-Barokah terlihat jelas kena cahaya
siang — dinding krem, kubah hijau — bukan siluet hitam seperti di malam hari. JILID 1–5 tetap
malam seperti semula.

**2. Boss modern.**

| JILID | Boss | Senjata | Huruf per serangan |
|---|---|---|---|
| 6 | Drone Pemburu | Roket Kembar | 5 |
| 7 | Mecha Baja | Meriam Plasma | 6 |
| 8 | Satelit Peretas | Paket Data | 7 |
| 9 | Kapal Siluman | Rudal Bayangan | 8 |
| 10 | Inti AI | Virus Inti | 10 |

Drone berbaling-baling yang berputar, robot dengan visor menyala, satelit berpanel surya,
kapal siluman bersudut tajam, dan sebagai penutup **Inti AI** dengan cincin-cincin berputar
mengelilingi inti yang berdenyut. JILID 10 jadi **checkpoint** kedua.

**3. Peluru memantul balik ke bossnya.** Ini yang paling terasa berbeda. Di JILID 1–5, peluru
yang hurufnya diketik langsung hancur di tempat. Mulai JILID 6, peluru itu **tidak hancur** —
ia menyala terang lalu **terbang balik ke arah bossnya** dan meledak di sana. Jadi darah boss
baru berkurang setelah peluru benar-benar sampai, bukan saat tombol ditekan. Boss yang bergerak
tetap dikejar peluru pantulannya.

JILID 1–5 sengaja **tidak diubah** dan tetap hancur seketika, supaya babak pertama yang sudah
disetujui tidak berubah rasanya.

**Kecepatan tidak dinaikkan**

Rencana lama menyebut kecepatan jatuh mulai dinaikkan di JILID 6 ke atas. Itu **dibatalkan**.
Kecepatan jatuh sekarang **sama persis di semua JILID 1–10**. Yang bertambah hanya banyaknya
huruf:

- meteor turun makin rapat (jeda 1,7 detik di JILID 1 → 0,7 detik di JILID 10),
- peluru boss per serangan makin banyak (1 → 10),
- kumpulan hurufnya melebar keluar home row mulai JILID 6: JILID 6 mulai masuk `Q W E R T`,
  JILID 7 seluruh baris atas, JILID 8–9 mulai baris bawah, JILID 10 penuh 29 huruf.

Dokumen `RENCANA_GAME_10_JARI.md` sudah diperbarui (bagian 10) supaya catatan lama soal
"kecepatan dinaikkan" tidak menyesatkan lagi.

**Berkas yang disentuh**

- Migrasi baru: 2 kolom di `meteor_game_levels` (`theme`, `bullet_returns`)
- `MeteorGameLevelSeeder` — sekarang berisi JILID 1–10
- `learner/meteor/play.blade.php` — langit pagi, 5 boss modern, mekanik pantul
- `learner/meteor/index.blade.php` — penanda "Pagi", "Peluru memantul", jumlah huruf
- `MeteorGameLevel` (model), `RENCANA_GAME_10_JARI.md`

**Perintah yang perlu dijalankan setelah menarik perubahan ini**

```
php artisan migrate --force
php artisan db:seed --class=MeteorGameLevelSeeder --force
php artisan optimize:clear
```

Seeder wajib dijalankan lagi — kalau tidak, JILID 6–10 tidak akan muncul dan JILID 1–5 tidak
akan punya penanda suasana.

**Yang perlu dicoba**

- Daftar JILID harus menampilkan **10 kartu**; JILID 6–10 bertanda "Pagi" dan "Peluru memantul".
- Main JILID 6 → langitnya harus **pagi**, bukan malam.
- Saat lawan Drone Pemburu, ketik huruf pada roketnya → roket harus **terbang balik ke drone**
  dulu, baru darah drone berkurang. Bukan langsung hilang di tempat.
- Main JILID 1 lagi → harus tetap malam, dan peluru tetap hancur seketika (tidak memantul).
- JILID 10 setelah tembus harus tercatat sebagai checkpoint kedua.

**Catatan pengujian**

Diuji otomatis di Chrome tanpa jendela: **27 dari 27 lolos untuk JILID 6** (termasuk dua
pemeriksaan khusus: darah boss TIDAK turun saat tombol ditekan, dan baru turun setelah peluru
sampai ke boss). **26 dari 26 lolos untuk JILID 1** sebagai pemeriksaan bahwa babak lama tidak
ikut berubah. Kelima boss baru sudah dipotret dan tampil benar.

Seperti sebelumnya, **sisi PHP/Laravel belum dijalankan** di komputer pengerjaan karena PHP
tidak terpasang di sana — migrasi dan seeder perlu dicoba sekali di server.

**Belum dikerjakan**

- Progres game belum tampil di Raport; masih hanya di daftar JILID.
- JILID 11 ke atas belum direncanakan.

---

## 18 September 2026 — Game 10 Jari: JILID 1–5 + lawan boss

**Masalah yang diperbaiki**

Game 10 Jari cuma punya satu arena tanpa ujung — main sampai nyawa habis, selesai, tidak
ada yang tersimpan. Padahal rencana yang sudah disepakati (`RENCANA_GAME_10_JARI.md`)
menyebut permainan bertingkat JILID 1–5 dan tiap JILID ditutup **lawan boss**. Bagian
itu belum pernah dibuat.

**Yang dikerjakan**

Sekarang alurnya berjenjang seperti menu Kuis dan Latihan Mengetik:

- Menu **Game 10 Jari** tidak langsung main, tapi menampilkan **daftar JILID** dengan
  status Terbuka / Terkunci / Tembus, sama seperti daftar tahap di Kuis.
- Satu JILID terdiri dari dua babak: **gelombang meteor** dulu (hancurkan sejumlah meteor),
  lalu **boss muncul** dengan palang darah di atas layar.
- Boss menyerang dengan memuntahkan beberapa huruf sekaligus. Tiap huruf yang berhasil
  diketik **mengurangi darah boss**; huruf yang lolos sampai bawah **mengurangi nyawa**.
- **Lulus satu JILID = boss kalah sebelum nyawa habis.** Lulus membuka JILID berikutnya.
- Nyawa (5) dipakai untuk satu rangkaian penuh: gelombang meteor + lawan boss.

Lima boss sesuai kesepakatan:

| JILID | Boss | Senjata | Huruf per serangan | Checkpoint |
|---|---|---|---|---|
| 1 | Pocong | Bola Api | 1 | — |
| 2 | Wewe Gombel | Bola Es Salju | 2 | — |
| 3 | Genderuwo | Rumput Bulat | 3 | — |
| 4 | Kelelawar | Anak Kelelawar | 4 | — |
| 5 | Orang naik UFO | Tembakan UFO | 4 | **Ya** |

Kelima boss digambar langsung di layar (tidak pakai berkas gambar), masing-masing dengan
warna peluru sendiri: api oranye, es biru, rumput hijau, kelelawar ungu, UFO hijau-toska.

**Checkpoint (JILID 5)**

- Gagal di JILID 1–4 → progres hangus, ulang dari **JILID 1**.
- Begitu boss JILID 5 kalah, **checkpoint tersimpan permanen**. Gagal setelah itu tidak
  melempar balik ke JILID 1 lagi, cukup ke checkpoint terakhir.

**Rekor tersimpan**

Tiap percobaan dicatat: lulus/tidak, sempat ketemu boss atau belum, jumlah yang
dihancurkan, WPM, dan akurasi. Di daftar JILID ditampilkan berapa kali tiap JILID sudah
dicoba dan WPM terbaik. WPM tetap **hanya tolok ukur**, bukan syarat lulus — targetnya
sekitar 25.

**Berkas yang disentuh**

- 3 migrasi baru: `meteor_game_levels`, `meteor_game_attempts`, dan 2 kolom progres di
  tabel `learners`
- Model baru `MeteorGameLevel`, `MeteorGameAttempt`; `Learner` ditambah kolom progres
- `MeteorGameLevelSeeder` (isi JILID 1–5)
- `MeteorGameController` (daftar JILID, halaman main, pencatatan hasil)
- View baru `learner/meteor/index.blade.php`; `learner/meteor/play.blade.php` ditambah babak boss
- `routes/web.php`, `learner/dashboard.blade.php`

**Perintah yang perlu dijalankan setelah menarik perubahan ini**

```
php artisan migrate --force
php artisan db:seed --class=MeteorGameLevelSeeder --force
php artisan optimize:clear
```

Seeder-nya aman dijalankan berulang. **Tanpa seeder, daftar JILID akan kosong.**

**Yang perlu dicoba**

- Buka **Game 10 Jari** → harus muncul 5 kartu JILID; hanya JILID 1 terbuka, sisanya terkunci.
- Main JILID 1 → hancurkan meteor sampai target → boss **Pocong** muncul dengan palang darah.
- Ketik huruf pada bola api → darah Pocong berkurang. Biarkan satu lolos → nyawa berkurang.
- Kalahkan Pocong → muncul layar menang + tombol **Lanjut JILID 2**, dan JILID 2 terbuka
  di daftar.
- Sengaja kalah → muncul catatan bahwa progres diulang dari JILID 1.
- Coba buka URL JILID yang masih terkunci langsung lewat alamat → harus ditolak dan
  dikembalikan ke daftar.

**Catatan pengujian**

Diuji otomatis di Chrome tanpa jendela dengan halaman permainan diekstrak jadi berkas
mandiri: **25 dari 25 pemeriksaan lolos** — termasuk pergantian gelombang→boss, darah boss
berkurang saat peluru dihancurkan, jalur menang, jalur kalah, dan data hasil yang dikirim
ke server. Kelima boss juga sudah dipotret dan tampil benar.

Seperti sebelumnya: **sisi PHP/Laravel-nya belum dijalankan** di komputer pengerjaan karena
PHP tidak terpasang di sana. Migrasi, seeder, controller, dan route baru **perlu dicoba
sekali di server** setelah deploy.

**Belum dikerjakan**

- JILID 6 ke atas (di JILID atas kecepatan mulai dinaikkan dan target WPM naik dari 25).
- Progres game ini belum tampil di Raport; baru terlihat di daftar JILID.

---

## 18 September 2026 — Game 10 Jari ditulis ulang (banyak bug + tampilan dirombak)

**Masalah yang diperbaiki**

Versi pratinjau Game 10 Jari banyak bugnya. Akar masalahnya satu: meteor dijatuhkan
memakai animasi CSS, sementara keputusan "meteor sudah mendarat" dihitung oleh
penghitung waktu yang **terpisah** dari animasi itu. Dua penghitung ini gampang lepas
sinkron, akibatnya:

- Kalau murid pindah tab sebentar lalu kembali, nyawanya sudah habis sendiri padahal
  meteornya masih kelihatan di tengah layar.
- Kalau komputernya berat/lemot, meteor kadang langsung melompat ke dasar layar.
- Menahan satu tombol dianggap sebagai puluhan ketikan.

Selain itu tampilannya dinilai kurang rapi, dan ada dua permintaan khusus: deretan
tombol `A S D F G H J K L ;` di bawah layar tidak diperlukan, dan meteor jangan cuma
jatuh di 10 jalur tetap yang mengikuti letak tombol di keyboard.

**Yang dikerjakan**

- Seluruh permainan ditulis ulang di atas satu "kanvas" dengan satu penghitung waktu
  saja, jadi yang terlihat di layar dan yang dihitung sistem selalu sama.
- **Deretan tombol virtual dihapus.** Layarnya sekarang murni langit + markas.
- **Posisi meteor sekarang acak menyebar** di seluruh lebar layar, tidak lagi terkunci
  pada letak tombol. Titik jatuhnya dipilih dengan cara mencari celah paling lebar dari
  meteor yang sudah ada, supaya tidak menumpuk di satu sisi.
- Gerakannya dibuat lebih alami: meteor melayang sedikit ke samping, berputar, dan
  ukurannya bervariasi. **Hurufnya tetap tegak** walaupun batunya berputar, jadi selalu
  terbaca.
- Permainan **otomatis berhenti (jeda)** saat tab ditinggal, dan bisa dijeda manual
  dengan tombol `Esc`.
- **Salah tekan tidak lagi mengurangi nyawa** — hanya menghanguskan kombo dan menurunkan
  akurasi. Nyawa berkurang murni kalau meteor berhasil mendarat.
- Tampilan baru: langit berbintang, siluet masjid berjendela menyala sebagai markas yang
  dijaga, sinar penembak dari markas ke meteor, ledakan, getaran layar saat kebobolan,
  dan penghitung kombo bertingkat.
- Kecepatan jatuh meteor **tetap** sepanjang permainan; yang bertambah hanyalah *jumlah*
  meteornya — sesuai kesepakatan di `RENCANA_GAME_10_JARI.md`.

**Berkas yang disentuh**

- `resources/views/learner/meteor/play.blade.php` (ditulis ulang)
- `resources/views/learner/dashboard.blade.php` (label kartu: "Pratinjau" → "Mode Bebas")
- `app/Http/Controllers/Learner/MeteorGameController.php` (hanya keterangan di komentar)

Tidak ada migrasi database dan tidak ada perubahan role, jadi saat deploy **cukup**
`git pull origin main` lalu `php artisan optimize:clear`.

**Yang perlu dicoba**

Buka menu **Game 10 Jari** dari akun murid, lalu:

- Tekan `SPASI` → meteor mulai turun di posisi yang berbeda-beda tiap kali.
- Ketik huruf pada meteor → meteor meledak dan ada sinar dari arah masjid.
- Tekan huruf yang tidak ada meteornya → nyawa harus **tetap**, hanya kombo yang hilang.
- Pindah ke tab lain lalu kembali → permainan harus dalam keadaan **jeda**, nyawa utuh.
- Tekan `Esc` untuk jeda, `SPASI` untuk lanjut.
- Coba juga lewat HP: tampilan harus menyesuaikan, dan muncul peringatan bahwa game ini
  butuh keyboard fisik.

**Catatan pengujian**

Diuji otomatis di browser (Chrome tanpa jendela) dengan halaman permainan diekstrak jadi
berkas mandiri: 28 dari 28 pemeriksaan perilaku lolos. Namun perlu dicatat, **halaman
Blade-nya sendiri belum pernah dijalankan lewat Laravel** karena PHP tidak terpasang di
komputer tempat pengerjaan ini — jadi tetap perlu dibuka sekali di server untuk memastikan.

**Belum dikerjakan**

- JILID 1–5, boss tiap JILID, checkpoint di JILID 5, dan penyimpanan rekor per murid.
  Semua itu butuh tabel baru `meteor_game_levels` / `meteor_game_attempts` dan jawaban
  atas pertanyaan terbuka di `RENCANA_GAME_10_JARI.md` bagian 7.
- Saat ini permainan masih "mode bebas": main terus sampai nyawa habis, hasilnya belum
  tersimpan ke database.

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
