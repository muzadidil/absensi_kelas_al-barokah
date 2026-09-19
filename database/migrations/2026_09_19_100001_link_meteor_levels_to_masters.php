<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * JILID sekarang merangkai master (nuansa/boss/peluru/efek) dan kecepatan jatuhnya
 * bisa diatur — selama ini terkunci 7 detik di dalam kode.
 *
 * Kolom lama theme/boss_name/boss_weapon_name dipindah jadi baris master dulu
 * (lihat backfill di bawah) supaya JILID yang sudah ada di server tidak kehilangan isinya,
 * baru kolomnya dibuang.
 */
return new class extends Migration
{
    private const SPRITES = [
        1 => 'pocong', 2 => 'wewe', 3 => 'genderuwo', 4 => 'kelelawar', 5 => 'ufo',
        6 => 'drone', 7 => 'mecha', 8 => 'satelit', 9 => 'kapal', 10 => 'inti_ai',
    ];

    private const HUES = [1 => 18, 2 => 190, 3 => 100, 4 => 280, 5 => 160,
                          6 => 12, 7 => 195, 8 => 130, 9 => 265, 10 => 310];

    private const BULLET_COLORS = [
        1  => ['#ffdca6', '#ff8b3d', '#8b2b07'],
        2  => ['#eaf7ff', '#7fd4ff', '#2f7fb5'],
        3  => ['#e6ffd9', '#8fd45a', '#2f6b1c'],
        4  => ['#e8dcff', '#9b7bd4', '#3d2470'],
        5  => ['#dcfff4', '#5fe6c0', '#136b52'],
        6  => ['#ffe0d0', '#ff6a3d', '#8f2408'],
        7  => ['#d9f5ff', '#3fc2ff', '#0a4f7a'],
        8  => ['#d8ffe6', '#39e07f', '#0d5c30'],
        9  => ['#ded6ff', '#7a5cf0', '#281c5c'],
        10 => ['#ffd9f4', '#ff4fc4', '#7a0d58'],
    ];

    public function up(): void
    {
        Schema::table('meteor_game_levels', function (Blueprint $table) {
            $table->foreignId('meteor_theme_id')->nullable()->after('display_label')
                  ->constrained('meteor_themes')->nullOnDelete();
            $table->foreignId('meteor_boss_id')->nullable()->after('meteor_theme_id')
                  ->constrained('meteor_bosses')->nullOnDelete();
            $table->foreignId('meteor_bullet_id')->nullable()->after('meteor_boss_id')
                  ->constrained('meteor_bullets')->nullOnDelete();
            $table->foreignId('meteor_effect_id')->nullable()->after('meteor_bullet_id')
                  ->constrained('meteor_effects')->nullOnDelete();
            $table->decimal('fall_seconds', 5, 2)->default(7)->after('spawn_interval_ms');
            $table->decimal('bullet_seconds', 5, 2)->default(5.2)->after('fall_seconds');
            $table->decimal('boss_shot_gap', 5, 2)->default(2.7)->after('bullet_seconds');
        });

        $this->backfill();

        Schema::table('meteor_game_levels', function (Blueprint $table) {
            $table->dropColumn(['theme', 'boss_name', 'boss_weapon_name']);
        });
    }

    private function backfill(): void
    {
        $now = now();

        $themeIds = [];
        foreach ($this->themeDefs() as $key => $def) {
            $themeIds[$key] = DB::table('meteor_themes')->where('name', $def['name'])->value('id')
                ?? DB::table('meteor_themes')->insertGetId($def + ['created_at' => $now, 'updated_at' => $now]);
        }

        $effectId = DB::table('meteor_effects')->where('name', 'Ledakan Standar')->value('id')
            ?? DB::table('meteor_effects')->insertGetId([
                'name' => 'Ledakan Standar', 'particle_count' => 20, 'particle_spread' => 150,
                'particle_hue' => 18, 'shake_strength' => 3, 'beam_color' => '#8ce6ff',
                'created_at' => $now, 'updated_at' => $now,
            ]);

        foreach (DB::table('meteor_game_levels')->get() as $level) {
            $n = (int) $level->level_number;

            $bossId = DB::table('meteor_bosses')->where('name', $level->boss_name)->value('id')
                ?? DB::table('meteor_bosses')->insertGetId([
                    'name' => $level->boss_name,
                    'sprite' => self::SPRITES[$n] ?? 'pocong',
                    'hue' => self::HUES[$n] ?? 18,
                    'created_at' => $now, 'updated_at' => $now,
                ]);

            $colors = self::BULLET_COLORS[$n] ?? self::BULLET_COLORS[1];
            $bulletId = DB::table('meteor_bullets')->where('name', $level->boss_weapon_name)->value('id')
                ?? DB::table('meteor_bullets')->insertGetId([
                    'name' => $level->boss_weapon_name,
                    'color_core' => $colors[0], 'color_mid' => $colors[1], 'color_edge' => $colors[2],
                    'meteor_effect_id' => $effectId,
                    'created_at' => $now, 'updated_at' => $now,
                ]);

            DB::table('meteor_game_levels')->where('id', $level->id)->update([
                'meteor_theme_id' => $themeIds[$level->theme] ?? $themeIds['malam'],
                'meteor_boss_id' => $bossId,
                'meteor_bullet_id' => $bulletId,
                'meteor_effect_id' => $effectId,
            ]);
        }
    }

    private function themeDefs(): array
    {
        return [
            'malam' => [
                'name' => 'Malam', 'sky_top' => '#04061a', 'sky_mid' => '#101a45', 'sky_bottom' => '#1d2764',
                'ground_top' => '#16371f', 'ground_bottom' => '#040c07', 'accent' => '#6ec8ff',
                'wall_color' => '#04150c', 'dome_color' => '#04150c',
                'sky_object' => 'bintang', 'is_dark' => true,
            ],
            'pagi' => [
                'name' => 'Pagi', 'sky_top' => '#2f6fb5', 'sky_mid' => '#7dbbe9', 'sky_bottom' => '#ffe6c2',
                'ground_top' => '#5aa84a', 'ground_bottom' => '#27622a', 'accent' => '#ffd682',
                'wall_color' => '#f2e7d0', 'dome_color' => '#2f8f6a',
                'sky_object' => 'awan', 'is_dark' => false,
            ],
        ];
    }

    public function down(): void
    {
        Schema::table('meteor_game_levels', function (Blueprint $table) {
            $table->string('theme')->default('malam')->after('display_label');
            $table->string('boss_name')->nullable();
            $table->string('boss_weapon_name')->nullable();

            $table->dropConstrainedForeignId('meteor_theme_id');
            $table->dropConstrainedForeignId('meteor_boss_id');
            $table->dropConstrainedForeignId('meteor_bullet_id');
            $table->dropConstrainedForeignId('meteor_effect_id');
            $table->dropColumn(['fall_seconds', 'bullet_seconds', 'boss_shot_gap']);
        });
    }
};
