<?php

namespace Database\Seeders;

use App\Models\TypingLevel;
use Illuminate\Database\Seeder;

class TypingLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'level_number' => 1,
                'name' => 'Tahap 1: Tombol Dasar (Home Row)',
                'allowed_keys' => 'asdfghjkl;',
                'word_bank' => <<<'WORDS'
                ada, akad, akal, akhlak, alas, alfa, alga, asah, asas, asal, dada, dadak, dahak, dahaga, falak, gada, gagah, gagak, gagal, gajah, galah, galak, gas, gasal, hadas, halal, jaga, jagal, jajah, jajak, jajal, jala, jalak, jas, kadal, kakak, kala, kalah, khas, lada, lafal, laga, lagak, laksa, sah, saga, saja, sajadah, sajak, salad, salah, salak

                alaska, alfa, dash, flash, gala, glad, halls, saga, salsa

                ad, ads, add, ah, aha, all, ash, ask, as, dad, dads, dash, dhal, fa, fad, fads, flag, flags, fall, falls, flash, flask, gag, gags, gal, gals, gala, galas, gall, galls, gas, gash, glad, glass, ha, had, hag, half, hall, halls, has, hash, jag, jags, la, lad, lads, lag, lags, lash, lass, sad, sag, sags, saga, salad, salsa, shah, shag, shags, ska, skald, slag, slags, slash, shall, alfalfa
                WORDS,
                'description' => 'Melatih tombol dasar: A S D F G H J K L ;',
            ],
            [
                'level_number' => 2,
                'name' => 'Tahap 2: Tombol Dasar + Baris Atas',
                'allowed_keys' => 'asdfghjkl;qwertyuiop',
                'word_bank' => <<<'WORDS'
                saya, kita, itu, apa, siapa, kata, kaki, hari, jauh, jarak, jari, kartu, kilat, lapar, lauk, lupa, gula, gigi, hati, hijau, helai, telur, tiga, tujuh, sepuluh, duduk, kakek, gigit, getar, keras, lari, lagu, layar, sedikit, asli, usia, usap, utara, salju, setuju, waktu, wajah, warga, warta, aduk, atas, atur, ayah, ayat, dasar, desa, duga, gerak, gigih, giat, hasil, ikat, ikut, jual, jurus, juta, kail, kait, kaku, kalau, karat, kasar, kasur, katak, kera, kerja, kertas, ketat, kikir, kuat, kuas, kuda, kue, kuku, kulit, kursi, laju, lalai, lalu, lapis, layak, lekat, lekas, letak, liar, lidah, liku, lipat, lirik, ludah, luka, lukis, lurus, lusuh, lutut, pasti, pagar, pagi, paksa, palsu, paras, parut, pasak, pasar, pasir, patah, patuh, pedas, peka, pelat, pelaut, peluk, pergi, peta, petak, petik, pijat, pikat, pikir, pilar, pilih, pisah, puas, pulau, pulih, pupuk, pusar, pusat, putar, putih, putus, rakit, rahasia, rajut, rakyat, rapat, rapi, rasa, rata, ratus, rawat, rekat, rela, resah, rias, rileks, rugi, rusak, rusuh, sadar, saku, salur, sapa, sapi, sapu, sarat, satu, sedia, segar, sejarah, sejati, sekat, selalu, selesai, seluruh, sepak, seru, sesak, sesat, setara, setia, siaga, siap, silau, sisa, sisi, sisik, sisir, sudah, suatu, suka, sukar, sulit, surat, surga, susah, susu, susul, susur, syarat, tadi, tahu, takut, tapak, tapi, tarik, tata, tawar, tegar, telah, telat, tepat, tepi, terap, terik, terus, tetap, tiap, tikar, tikus, tiru, titik, tugas, tuju, tuli, tulis, turut, tutup, udara, ukir, ukur, ulas, ular, usaha, usik, usir, usul, usut, utuh, wajar, wasit
                WORDS,
                'description' => 'Melatih tombol dasar A S D F G H J K L ; ditambah baris atas Q W E R T Y U I O P',
            ],
            [
                'level_number' => 3,
                'name' => 'Tahap 3: Tombol Dasar + Baris Atas + Baris Bawah',
                'allowed_keys' => 'asdfghjkl;qwertyuiopzxcvbnm,.',
                'word_bank' => <<<'WORDS'
                belajar, mengajar, murid, guru, sekolah, kelas, pelajaran, ulangan, ujian, nilai, jawaban, pertanyaan, tugas, buku, pensil, pulpen, papan, tulis, membaca, menulis, menghitung, matematika, bahasa, indonesia, inggris, ilmu, pengetahuan, alam, sosial, agama, sejarah, olahraga, istirahat, teman, sahabat, keluarga, orang, tua, ayah, ibu, kakak, adik, rumah, jalan, sepeda, motor, mobil, waktu, pagi, siang, sore, malam, senin, selasa, rabu, kamis, jumat, sabtu, minggu, januari, februari, maret, april, mei, juni, juli, agustus, september, oktober, november, desember, cerdas, rajin, disiplin, jujur, sopan, santun, semangat, cita, impian, masa, depan, cinta, damai, bersih, sehat, kuat, cepat, lambat, benar, salah, mudah, susah, senang, sedih, marah, takut, berani, percaya, diri, hormat, patuh, taat, ikhlas, sabar, syukur, doa, mimpi
                WORDS,
                'description' => 'Melatih semua baris: A S D F G H J K L ;, Q W E R T Y U I O P, dan Z X C V B N M , .',
            ],
        ];

        foreach ($levels as $level) {
            TypingLevel::updateOrCreate(['level_number' => $level['level_number']], $level);
        }
    }
}
