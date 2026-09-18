<?php

namespace Database\Seeders;

use App\Models\MeteorGameLevel;
use Illuminate\Database\Seeder;

/**
 * JILID 1-5 persis tabel di RENCANA_GAME_10_JARI.md §3.5.
 * Aman dijalankan berulang (updateOrCreate berdasarkan level_number).
 *
 * Kecepatan jatuh meteor sengaja TIDAK ada di sini — kecepatannya konstan,
 * yang naik tiap JILID adalah wave_target & kerapatan spawn (§3.3).
 */
class MeteorGameLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'level_number' => 1,
                'display_label' => 'JILID 1',
                'wave_target' => 10,
                'spawn_interval_ms' => 1700,
                'boss_name' => 'Pocong',
                'boss_weapon_name' => 'Bola Api',
                'boss_bullets_per_shot' => 1,
                'boss_hp' => 6,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 2,
                'display_label' => 'JILID 2',
                'wave_target' => 14,
                'spawn_interval_ms' => 1450,
                'boss_name' => 'Wewe Gombel',
                'boss_weapon_name' => 'Bola Es Salju',
                'boss_bullets_per_shot' => 2,
                'boss_hp' => 10,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 3,
                'display_label' => 'JILID 3',
                'wave_target' => 18,
                'spawn_interval_ms' => 1250,
                'boss_name' => 'Genderuwo',
                'boss_weapon_name' => 'Rumput Bulat',
                'boss_bullets_per_shot' => 3,
                'boss_hp' => 14,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 4,
                'display_label' => 'JILID 4',
                'wave_target' => 22,
                'spawn_interval_ms' => 1050,
                'boss_name' => 'Kelelawar',
                'boss_weapon_name' => 'Anak Kelelawar',
                'boss_bullets_per_shot' => 4,
                'boss_hp' => 18,
                'is_checkpoint' => false,
            ],
            [
                'level_number' => 5,
                'display_label' => 'JILID 5',
                'wave_target' => 26,
                'spawn_interval_ms' => 900,
                'boss_name' => 'Orang naik UFO',
                'boss_weapon_name' => 'Tembakan UFO',
                'boss_bullets_per_shot' => 4,
                'boss_hp' => 24,
                'is_checkpoint' => true,
            ],
        ];

        foreach ($levels as $level) {
            MeteorGameLevel::updateOrCreate(
                ['level_number' => $level['level_number']],
                $level + ['allowed_keys' => 'asdfghjkl;', 'lives' => 5]
            );
        }
    }
}
