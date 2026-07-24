# Rencana: Kuis Pilihan Ganda Berjenjang (pengganti Tugas) + CMD Terminal

> Catatan hasil diskusi. **Belum dikerjakan** — dokumen ini acuan sebelum implementasi.
> Terakhir diperbarui: 2026-07-25.

## 1. Filosofi

**Kunci utamanya PENGULANGAN, bukan kecerdasan.** Makin sering murid mengulang, makin nempel.
Semua mekanik dirancang untuk memaksa latihan berulang sampai paham — mirip game jadul
(salah sedikit, ulang lagi).

## 2. Ringkasan Perubahan

| Bagian | Keputusan |
|---|---|
| Soal **Esai** | **Dihapus total** (termasuk data lama). Digantikan modul **CMD/Terminal** (fase nanti). |
| Soal **Praktek** | **Dihapus total** (termasuk data lama). Sudah diwakili Typing Test. |
| **Tugas** | Diubah jadi **Kuis Pilihan Ganda berjenjang** — kembaran Typing Test. |
| **Raport** | Tetap ada, menampilkan **progres + total percobaan + predikat** (fokus kegigihan, tapi tetap ada angka). |
| **Istilah KRS/KHS** | Diabaikan — cukup tampil di Raport. |
| **CMD/Terminal** | **Fase berikutnya.** Tab sidebar sendiri, **terkunci sampai Typing Test 100%**. |

## 3. Desain Kuis Pilihan Ganda (MC)

Dibuat **persis pola Typing Test**: tangga tahap berjenjang + kunci progresif.

### 3.1 Struktur
- **Tahap 1, 2, 3, … N** — satu tangga **global** (semua murid tangganya sama, seperti Typing).
  *(ASUMSI — perlu konfirmasi, lihat §7.)*
- Tiap tahap punya **bank soal**, jumlah bebas ditentukan guru (5 / 10 / 20 / dst).
- Tahap 1 dibuat paling mudah, makin naik makin sulit (terserah guru).
- Tiap **soal** memuat: pertanyaan, beberapa **opsi jawaban**, **1 opsi benar**, dan **penjelasan**.

### 3.2 Mekanik Bermain (murid)
1. Soal ditampilkan **satu per satu** (soal berikutnya terkunci sampai soal sekarang tuntas).
2. Jawaban **BENAR** → lanjut ke soal berikutnya.
3. Jawaban **SALAH** → tampilkan **jawaban yang benar + penjelasannya**, lalu **ulang dari soal 1**
   pada tahap itu (semua progres soal di tahap itu hangus).
4. **Soal diacak** urutannya **dan opsi jawaban (A/B/C/D) diacak posisinya** setiap kali mulai/ulang
   → murid tidak bisa menghafal "jawabannya B", harus paham isinya.
5. **Lulus tahap** = berhasil menjawab **semua soal benar dalam satu run bersih tanpa salah**.
6. Lulus tahap → **buka tahap berikutnya** (kunci progresif, seperti Typing).

### 3.3 Toggle "Mode Pamungkas" — **per tahap**
- Default (off): gagal → ulang **tahap itu saja**.
- On: gagal di tahap ini → **balik ke Tahap 1**, **semua kunci tertutup lagi**.
- Dipasang **per tahap** (fleksibel, konsisten dengan pola per-tahap Typing). Contoh: Tahap 1–9
  normal, Tahap 10 dinyalakan sebagai gauntlet pamungkas di akhir.

## 4. Rancangan Data (usulan — tabel baru, sejajar Typing)

Bikin tabel baru `quiz_*` meniru `typing_*`, lalu **pensiunkan** sistem assignment lama.
*(Alternatif: mendaur ulang tabel assignment — tidak disarankan karena mekaniknya beda jauh.)*

- **`quiz_levels`** (mirip `typing_levels`)
  - `level_number` (unik, urutan tahap)
  - `name`, `description`
  - `reset_to_first_on_fail` (boolean) — toggle Mode Pamungkas per tahap
- **`quiz_questions`**
  - `quiz_level_id`, `question_text`, `explanation`, `sort_order`
- **`quiz_options`**
  - `quiz_question_id`, `option_text`, `is_correct` (boolean; tepat 1 benar per soal)
  - *disimpan sebagai daftar opsi → memudahkan pengacakan posisi*
- **`quiz_attempts`** (mirip `typing_attempts`)
  - `learner_id`, `quiz_level_id`, `passed` (boolean), `created_at`
  - dipakai untuk: status lulus per tahap, kunci progresif, hitung total percobaan
  - *(catatan: tiap "run" = 1 attempt; passed=true kalau run bersih tuntas)*

Kunci/unlock **diturunkan dari `quiz_attempts`** persis logika Typing (tahap N terbuka bila
tahap sebelumnya `passed`). Untuk tahap ber-`reset_to_first_on_fail`, kegagalan menutup semua
kunci (progres lulus di-reset untuk tangga tsb).

## 5. Raport MC (fokus kegigihan, tetap ada angka)

Per murid ditampilkan:
- **Progres** = tahap tembus ÷ total tahap (mis. 7/10 = 70%).
- **Total percobaan** = jumlah seluruh run/percobaan → indikator kegigihan.
- **Predikat** dari progres (mis. 100% = "Tuntas", dst).

Guru bisa langsung baca: "A baru tahap 3 tapi 120 percobaan → gigih tapi masih struggle"
vs "B tahap 10 dalam 15 percobaan → cepat nangkap".

## 6. Yang Dihapus / Terdampak (sistem lama)

Perlu dibersihkan saat implementasi:
- Tipe soal `essay` & `praktek` di seluruh alur (form guru, tampilan, penilaian).
- Alur **penilaian manual** admin (esai/praktek) — tidak dipakai lagi.
- Data lama esai/praktek di DB → **dibuang** (migrasi hapus / bersih-bersih).
- File/route terdampak (indikatif): `Guru/AssignmentQuestionController`, `Admin/AssignmentController`
  (nilai esai), view `assignments/*` (pilgan/essay/praktek), `LearnerAnswer` alur esai.
- Keputusan: apakah tabel `assignments`, `assignment_questions`, `assignment_learners`,
  `learner_answers` **dipensiunkan** total dan diganti `quiz_*` (disarankan), atau sebagian didaur ulang.

## 7. Pertanyaan Terbuka (konfirmasi sebelum bangun)

1. **Tangga global vs per kelas/mapel** — asumsi saat ini: **satu tangga global** seperti Typing.
   Betul, atau kuis dipisah per kelas/mata pelajaran?
2. **Angka raport** — apakah *Progres + Total Percobaan + Predikat* sudah cukup, atau ada angka lain?
3. **Tabel lama** — setuju **pensiunkan `assignment_*` + `learner_answers`** dan pakai `quiz_*` baru?

## 8. Urutan Pengerjaan (roadmap)

- **Fase 0** — Konfirmasi §7, lalu migrasi: hapus data/tipe esai & praktek; siapkan tabel `quiz_*`.
- **Fase 1 — Master MC (Guru):** CRUD tahap + soal + opsi + penjelasan + toggle Pamungkas per tahap.
  (Tambah tombol **Salin tahap** seperti di Typing.)
- **Fase 2 — Main MC (Murid):** engine gauntlet — soal 1-per-1, acak soal & opsi, benar→lanjut,
  salah→tampil kunci+penjelasan→reset, kunci progresif, mode pamungkas.
- **Fase 3 — Raport MC:** progres + total percobaan + predikat (murid & admin).
- **Fase 4 (NANTI) — CMD/Terminal:** tab sidebar "CMD", **terkunci sampai Typing Test 100%**.
  Detail dirancang terpisah setelah MC kelar.

## 9. Prinsip Reuse

Sebisa mungkin **pakai ulang pola Typing Test** yang sudah terbukti:
- Logika kunci progresif (tahap terbuka bila tahap sebelumnya lulus).
- Pola "attempt" + status lulus.
- Layout master Guru (kartu tahap, modal tambah/edit, tombol salin, chip info).
- Layout murid (kartu tahap: terbuka/terkunci/lulus + rekor).
