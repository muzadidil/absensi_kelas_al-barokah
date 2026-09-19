<?php

namespace Database\Seeders;

use App\Models\MeteorBoss;
use App\Models\MeteorBullet;
use App\Models\MeteorEffect;
use App\Models\MeteorGameLevel;
use App\Models\MeteorTheme;
use Illuminate\Database\Seeder;

/**
 * Isi awal Game 10 Jari. Semuanya cuma NILAI DEFAULT — admin bebas mengubahnya
 * lewat menu Game 10 Jari, dan seeder ini tidak akan menimpanya selama nama
 * masternya tidak diubah (updateOrCreate berdasarkan nama / level_number).
 *
 * Keseimbangan JILID 6-10 sengaja dibuat landai: saat huruf BARU diperkenalkan,
 * tekanan lain (jumlah peluru & kerapatan meteor) justru diturunkan, supaya yang
 * sulit cuma satu hal pada satu waktu.
 */
class MeteorGameLevelSeeder extends Seeder
{
    private const HOME = 'asdfghjkl;';

    public function run(): void
    {
        $themes = $this->seedThemes();
        $effects = $this->seedEffects();
        $bullets = $this->seedBullets($effects);
        $bosses = $this->seedBosses();

        foreach ($this->levels() as $row) {
            MeteorGameLevel::updateOrCreate(
                ['level_number' => $row['level_number']],
                [
                    'display_label' => $row['display_label'],
                    'meteor_theme_id' => $themes[$row['theme']]->id,
                    'meteor_boss_id' => $bosses[$row['boss']]->id,
                    'meteor_bullet_id' => $bullets[$row['bullet']]->id,
                    'meteor_effect_id' => $effects[$row['effect']]->id,
                    'allowed_keys' => $row['keys'],
                    'lives' => 5,
                    'wave_target' => $row['wave'],
                    'spawn_interval_ms' => $row['spawn'],
                    'fall_seconds' => $row['fall'],
                    'bullet_seconds' => $row['fall'] - 1.5,
                    'boss_shot_gap' => $row['gap'],
                    'boss_bullets_per_shot' => $row['bullets'],
                    'boss_hp' => $row['hp'],
                    'bullet_returns' => $row['returns'],
                    'is_checkpoint' => $row['checkpoint'],
                ]
            );
        }
    }

    private function seedThemes(): array
    {
        $defs = [
            'malam' => ['name' => 'Malam', 'sky_top' => '#04061a', 'sky_mid' => '#101a45', 'sky_bottom' => '#1d2764',
                        'ground_top' => '#16371f', 'ground_bottom' => '#040c07', 'accent' => '#6ec8ff',
                        'wall_color' => '#04150c', 'dome_color' => '#04150c', 'sky_object' => 'bintang', 'is_dark' => true],
            'pagi'  => ['name' => 'Pagi', 'sky_top' => '#2f6fb5', 'sky_mid' => '#7dbbe9', 'sky_bottom' => '#ffe6c2',
                        'ground_top' => '#5aa84a', 'ground_bottom' => '#27622a', 'accent' => '#ffd682',
                        'wall_color' => '#f2e7d0', 'dome_color' => '#2f8f6a', 'sky_object' => 'awan', 'is_dark' => false],
            'senja' => ['name' => 'Senja', 'sky_top' => '#2b1b4d', 'sky_mid' => '#8c4a6b', 'sky_bottom' => '#ffb27a',
                        'ground_top' => '#3d3320', 'ground_bottom' => '#140f08', 'accent' => '#ffa45c',
                        'wall_color' => '#2a2016', 'dome_color' => '#5c3a2a', 'sky_object' => 'awan', 'is_dark' => true],
        ];

        $out = [];
        foreach ($defs as $key => $def) {
            $out[$key] = MeteorTheme::firstOrCreate(['name' => $def['name']], $def);
        }

        return $out;
    }

    private function seedEffects(): array
    {
        $defs = [
            'api'   => ['name' => 'Ledakan Api', 'particle_count' => 20, 'particle_spread' => 150, 'particle_hue' => 18,  'shake_strength' => 3, 'beam_color' => '#ff8232'],
            'es'    => ['name' => 'Pecahan Es', 'particle_count' => 22, 'particle_spread' => 130, 'particle_hue' => 190, 'shake_strength' => 2, 'beam_color' => '#8ce6ff'],
            'daun'  => ['name' => 'Serpihan Daun', 'particle_count' => 18, 'particle_spread' => 140, 'particle_hue' => 100, 'shake_strength' => 3, 'beam_color' => '#9ae65f'],
            'gelap' => ['name' => 'Debu Gelap', 'particle_count' => 24, 'particle_spread' => 160, 'particle_hue' => 280, 'shake_strength' => 4, 'beam_color' => '#b79bff'],
            'data'  => ['name' => 'Ledakan Data', 'particle_count' => 26, 'particle_spread' => 170, 'particle_hue' => 130, 'shake_strength' => 3, 'beam_color' => '#5fffa8'],
            'inti'  => ['name' => 'Ledakan Inti', 'particle_count' => 30, 'particle_spread' => 190, 'particle_hue' => 310, 'shake_strength' => 5, 'beam_color' => '#ff7ad4'],
        ];

        $out = [];
        foreach ($defs as $key => $def) {
            $out[$key] = MeteorEffect::firstOrCreate(['name' => $def['name']], $def);
        }

        return $out;
    }

    private function seedBullets(array $effects): array
    {
        $defs = [
            'api'    => ['name' => 'Bola Api', 'color_core' => '#ffdca6', 'color_mid' => '#ff8b3d', 'color_edge' => '#8b2b07', 'effect' => 'api'],
            'es'     => ['name' => 'Bola Es Salju', 'color_core' => '#eaf7ff', 'color_mid' => '#7fd4ff', 'color_edge' => '#2f7fb5', 'effect' => 'es'],
            'rumput' => ['name' => 'Rumput Bulat', 'color_core' => '#e6ffd9', 'color_mid' => '#8fd45a', 'color_edge' => '#2f6b1c', 'effect' => 'daun'],
            'anak'   => ['name' => 'Anak Kelelawar', 'color_core' => '#e8dcff', 'color_mid' => '#9b7bd4', 'color_edge' => '#3d2470', 'effect' => 'gelap'],
            'ufo'    => ['name' => 'Tembakan UFO', 'color_core' => '#dcfff4', 'color_mid' => '#5fe6c0', 'color_edge' => '#136b52', 'effect' => 'data'],
            'roket'  => ['name' => 'Roket Kembar', 'color_core' => '#ffe0d0', 'color_mid' => '#ff6a3d', 'color_edge' => '#8f2408', 'effect' => 'api'],
            'plasma' => ['name' => 'Meriam Plasma', 'color_core' => '#d9f5ff', 'color_mid' => '#3fc2ff', 'color_edge' => '#0a4f7a', 'effect' => 'es'],
            'paket'  => ['name' => 'Paket Data', 'color_core' => '#d8ffe6', 'color_mid' => '#39e07f', 'color_edge' => '#0d5c30', 'effect' => 'data'],
            'bayang' => ['name' => 'Rudal Bayangan', 'color_core' => '#ded6ff', 'color_mid' => '#7a5cf0', 'color_edge' => '#281c5c', 'effect' => 'gelap'],
            'virus'  => ['name' => 'Virus Inti', 'color_core' => '#ffd9f4', 'color_mid' => '#ff4fc4', 'color_edge' => '#7a0d58', 'effect' => 'inti'],
        ];

        $out = [];
        foreach ($defs as $key => $def) {
            $effectKey = $def['effect'];
            unset($def['effect']);
            $def['meteor_effect_id'] = $effects[$effectKey]->id;
            $out[$key] = MeteorBullet::firstOrCreate(['name' => $def['name']], $def);
        }

        return $out;
    }

    private function seedBosses(): array
    {
        $defs = [
            'pocong'    => ['name' => 'Pocong', 'sprite' => 'pocong', 'hue' => 18],
            'wewe'      => ['name' => 'Wewe Gombel', 'sprite' => 'wewe', 'hue' => 190],
            'genderuwo' => ['name' => 'Genderuwo', 'sprite' => 'genderuwo', 'hue' => 100],
            'kelelawar' => ['name' => 'Kelelawar', 'sprite' => 'kelelawar', 'hue' => 280],
            'ufo'       => ['name' => 'Orang naik UFO', 'sprite' => 'ufo', 'hue' => 160],
            'drone'     => ['name' => 'Drone Pemburu', 'sprite' => 'drone', 'hue' => 12],
            'mecha'     => ['name' => 'Mecha Baja', 'sprite' => 'mecha', 'hue' => 195],
            'satelit'   => ['name' => 'Satelit Peretas', 'sprite' => 'satelit', 'hue' => 130],
            'kapal'     => ['name' => 'Kapal Siluman', 'sprite' => 'kapal', 'hue' => 265],
            'inti'      => ['name' => 'Inti AI', 'sprite' => 'inti_ai', 'hue' => 310],
        ];

        $out = [];
        foreach ($defs as $key => $def) {
            $out[$key] = MeteorBoss::firstOrCreate(['name' => $def['name']], $def);
        }

        return $out;
    }

    /**
     * Angka di sini adalah titik awal yang aman, bukan harga mati.
     * Kolom 'keys' sengaja melebar dua huruf per JILID mulai JILID 6 (E dan I dulu —
     * jari telunjuk naik), bukan langsung satu baris penuh seperti versi sebelumnya
     * yang ternyata terlalu berat.
     */
    private function levels(): array
    {
        return [
            ['level_number' => 1, 'display_label' => 'JILID 1', 'theme' => 'malam', 'boss' => 'pocong',
             'bullet' => 'api', 'effect' => 'api', 'keys' => self::HOME,
             'wave' => 10, 'spawn' => 1700, 'fall' => 7.0, 'gap' => 3.0, 'bullets' => 1, 'hp' => 6,
             'returns' => false, 'checkpoint' => false],

            ['level_number' => 2, 'display_label' => 'JILID 2', 'theme' => 'malam', 'boss' => 'wewe',
             'bullet' => 'es', 'effect' => 'es', 'keys' => self::HOME,
             'wave' => 12, 'spawn' => 1500, 'fall' => 7.0, 'gap' => 2.9, 'bullets' => 2, 'hp' => 10,
             'returns' => false, 'checkpoint' => false],

            ['level_number' => 3, 'display_label' => 'JILID 3', 'theme' => 'malam', 'boss' => 'genderuwo',
             'bullet' => 'rumput', 'effect' => 'daun', 'keys' => self::HOME,
             'wave' => 14, 'spawn' => 1350, 'fall' => 7.0, 'gap' => 2.8, 'bullets' => 2, 'hp' => 12,
             'returns' => false, 'checkpoint' => false],

            ['level_number' => 4, 'display_label' => 'JILID 4', 'theme' => 'malam', 'boss' => 'kelelawar',
             'bullet' => 'anak', 'effect' => 'gelap', 'keys' => self::HOME,
             'wave' => 16, 'spawn' => 1200, 'fall' => 7.0, 'gap' => 2.7, 'bullets' => 3, 'hp' => 15,
             'returns' => false, 'checkpoint' => false],

            ['level_number' => 5, 'display_label' => 'JILID 5', 'theme' => 'malam', 'boss' => 'ufo',
             'bullet' => 'ufo', 'effect' => 'data', 'keys' => self::HOME,
             'wave' => 18, 'spawn' => 1100, 'fall' => 7.0, 'gap' => 2.7, 'bullets' => 3, 'hp' => 18,
             'returns' => false, 'checkpoint' => true],

            // Babak pagi. Huruf baru masuk -> tekanan lain diturunkan dulu.
            ['level_number' => 6, 'display_label' => 'JILID 6', 'theme' => 'pagi', 'boss' => 'drone',
             'bullet' => 'roket', 'effect' => 'api', 'keys' => self::HOME . 'ei',
             'wave' => 16, 'spawn' => 1450, 'fall' => 7.5, 'gap' => 3.0, 'bullets' => 2, 'hp' => 14,
             'returns' => true, 'checkpoint' => false],

            ['level_number' => 7, 'display_label' => 'JILID 7', 'theme' => 'pagi', 'boss' => 'mecha',
             'bullet' => 'plasma', 'effect' => 'es', 'keys' => self::HOME . 'eiru',
             'wave' => 18, 'spawn' => 1350, 'fall' => 7.5, 'gap' => 2.9, 'bullets' => 3, 'hp' => 17,
             'returns' => true, 'checkpoint' => false],

            ['level_number' => 8, 'display_label' => 'JILID 8', 'theme' => 'pagi', 'boss' => 'satelit',
             'bullet' => 'paket', 'effect' => 'data', 'keys' => self::HOME . 'eiruwo',
             'wave' => 20, 'spawn' => 1250, 'fall' => 7.0, 'gap' => 2.8, 'bullets' => 3, 'hp' => 20,
             'returns' => true, 'checkpoint' => false],

            ['level_number' => 9, 'display_label' => 'JILID 9', 'theme' => 'pagi', 'boss' => 'kapal',
             'bullet' => 'bayang', 'effect' => 'gelap', 'keys' => self::HOME . 'eiruwoqpty',
             'wave' => 22, 'spawn' => 1200, 'fall' => 7.0, 'gap' => 2.7, 'bullets' => 4, 'hp' => 24,
             'returns' => true, 'checkpoint' => false],

            ['level_number' => 10, 'display_label' => 'JILID 10', 'theme' => 'senja', 'boss' => 'inti',
             'bullet' => 'virus', 'effect' => 'inti', 'keys' => 'asdfghjkl;qwertyuiopzxcvbnm',
             'wave' => 24, 'spawn' => 1100, 'fall' => 7.0, 'gap' => 2.6, 'bullets' => 4, 'hp' => 28,
             'returns' => true, 'checkpoint' => true],
        ];
    }
}
