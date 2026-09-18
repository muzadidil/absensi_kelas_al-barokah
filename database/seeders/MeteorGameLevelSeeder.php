<?php

namespace Database\Seeders;

use App\Models\MeteorGameLevel;
use Illuminate\Database\Seeder;

/**
 * JILID 1-5 persis tabel di RENCANA_GAME_10_JARI.md §3.5 (boss cerita rakyat, suasana malam).
 * JILID 6-10 lanjutannya: suasana pagi, boss modern, dan peluru yang memantul balik ke boss.
 *
 * Kecepatan jatuh TIDAK pernah dinaikkan di JILID mana pun. Yang bertambah cuma jumlah
 * huruf: kerapatan spawn makin rapat, peluru per serangan makin banyak, dan mulai JILID 6
 * kumpulan hurufnya melebar keluar home row (§8 "Fase 4").
 */
class MeteorGameLevelSeeder extends Seeder
{
    private const HOME = 'asdfghjkl;';
    private const HOME_TOP = 'asdfghjkl;qwertyuiop';
    private const HOME_TOP_BOTTOM = 'asdfghjkl;qwertyuiopzxcvbnm,.';

    public function run(): void
    {
        $levels = [
            [
                'level_number' => 1,
                'display_label' => 'JILID 1',
                'theme' => 'malam',
                'allowed_keys' => self::HOME,
                'wave_target' => 10,
                'spawn_interval_ms' => 1700,
                'boss_name' => 'Pocong',
                'boss_weapon_name' => 'Bola Api',
                'boss_bullets_per_shot' => 1,
                'boss_hp' => 6,
                'bullet_returns' => false,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 2,
                'display_label' => 'JILID 2',
                'theme' => 'malam',
                'allowed_keys' => self::HOME,
                'wave_target' => 14,
                'spawn_interval_ms' => 1450,
                'boss_name' => 'Wewe Gombel',
                'boss_weapon_name' => 'Bola Es Salju',
                'boss_bullets_per_shot' => 2,
                'boss_hp' => 10,
                'bullet_returns' => false,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 3,
                'display_label' => 'JILID 3',
                'theme' => 'malam',
                'allowed_keys' => self::HOME,
                'wave_target' => 18,
                'spawn_interval_ms' => 1250,
                'boss_name' => 'Genderuwo',
                'boss_weapon_name' => 'Rumput Bulat',
                'boss_bullets_per_shot' => 3,
                'boss_hp' => 14,
                'bullet_returns' => false,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 4,
                'display_label' => 'JILID 4',
                'theme' => 'malam',
                'allowed_keys' => self::HOME,
                'wave_target' => 22,
                'spawn_interval_ms' => 1050,
                'boss_name' => 'Kelelawar',
                'boss_weapon_name' => 'Anak Kelelawar',
                'boss_bullets_per_shot' => 4,
                'boss_hp' => 18,
                'bullet_returns' => false,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 5,
                'display_label' => 'JILID 5',
                'theme' => 'malam',
                'allowed_keys' => self::HOME,
                'wave_target' => 26,
                'spawn_interval_ms' => 900,
                'boss_name' => 'Orang naik UFO',
                'boss_weapon_name' => 'Tembakan UFO',
                'boss_bullets_per_shot' => 4,
                'boss_hp' => 24,
                'bullet_returns' => false,
                'is_checkpoint' => true,
            ],

            // --- Babak pagi: boss modern, peluru memantul balik ---
            [
                'level_number' => 6,
                'display_label' => 'JILID 6',
                'theme' => 'pagi',
                'allowed_keys' => 'asdfghjkl;qwert',
                'wave_target' => 28,
                'spawn_interval_ms' => 860,
                'boss_name' => 'Drone Pemburu',
                'boss_weapon_name' => 'Roket Kembar',
                'boss_bullets_per_shot' => 5,
                'boss_hp' => 28,
                'bullet_returns' => true,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 7,
                'display_label' => 'JILID 7',
                'theme' => 'pagi',
                'allowed_keys' => self::HOME_TOP,
                'wave_target' => 32,
                'spawn_interval_ms' => 820,
                'boss_name' => 'Mecha Baja',
                'boss_weapon_name' => 'Meriam Plasma',
                'boss_bullets_per_shot' => 6,
                'boss_hp' => 32,
                'bullet_returns' => true,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 8,
                'display_label' => 'JILID 8',
                'theme' => 'pagi',
                'allowed_keys' => 'asdfghjkl;qwertyuiopzxcv',
                'wave_target' => 36,
                'spawn_interval_ms' => 780,
                'boss_name' => 'Satelit Peretas',
                'boss_weapon_name' => 'Paket Data',
                'boss_bullets_per_shot' => 7,
                'boss_hp' => 36,
                'bullet_returns' => true,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 9,
                'display_label' => 'JILID 9',
                'theme' => 'pagi',
                'allowed_keys' => 'asdfghjkl;qwertyuiopzxcvbnm',
                'wave_target' => 40,
                'spawn_interval_ms' => 740,
                'boss_name' => 'Kapal Siluman',
                'boss_weapon_name' => 'Rudal Bayangan',
                'boss_bullets_per_shot' => 8,
                'boss_hp' => 42,
                'bullet_returns' => true,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 10,
                'display_label' => 'JILID 10',
                'theme' => 'pagi',
                'allowed_keys' => self::HOME_TOP_BOTTOM,
                'wave_target' => 45,
                'spawn_interval_ms' => 700,
                'boss_name' => 'Inti AI',
                'boss_weapon_name' => 'Virus Inti',
                'boss_bullets_per_shot' => 10,
                'boss_hp' => 50,
                'bullet_returns' => true,
                'is_checkpoint' => true,
            ],
        ];

        foreach ($levels as $level) {
            MeteorGameLevel::updateOrCreate(
                ['level_number' => $level['level_number']],
                $level + ['lives' => 5]
            );
        }
    }
}
