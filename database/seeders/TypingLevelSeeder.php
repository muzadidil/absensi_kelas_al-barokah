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
                'description' => 'Melatih tombol dasar A S D F G H J K L ; ditambah baris atas Q W E R T Y U I O P',
            ],
            [
                'level_number' => 3,
                'name' => 'Tahap 3: Tombol Dasar + Baris Atas + Baris Bawah',
                'allowed_keys' => 'asdfghjkl;qwertyuiopzxcvbnm,.',
                'description' => 'Melatih semua baris: A S D F G H J K L ;, Q W E R T Y U I O P, dan Z X C V B N M , .',
            ],
        ];

        foreach ($levels as $level) {
            TypingLevel::updateOrCreate(['level_number' => $level['level_number']], $level);
        }
    }
}
