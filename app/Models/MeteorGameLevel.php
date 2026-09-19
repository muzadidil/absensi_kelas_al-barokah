<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeteorGameLevel extends Model
{
    protected $fillable = [
        'level_number',
        'display_label',
        'meteor_theme_id',
        'meteor_boss_id',
        'meteor_bullet_id',
        'meteor_effect_id',
        'allowed_keys',
        'lives',
        'wave_target',
        'spawn_interval_ms',
        'fall_seconds',
        'bullet_seconds',
        'boss_shot_gap',
        'boss_bullets_per_shot',
        'boss_hp',
        'bullet_returns',
        'is_checkpoint',
    ];

    protected $casts = [
        'is_checkpoint' => 'boolean',
        'bullet_returns' => 'boolean',
        'fall_seconds' => 'float',
        'bullet_seconds' => 'float',
        'boss_shot_gap' => 'float',
    ];

    public function theme()
    {
        return $this->belongsTo(MeteorTheme::class, 'meteor_theme_id');
    }

    public function boss()
    {
        return $this->belongsTo(MeteorBoss::class, 'meteor_boss_id');
    }

    public function bullet()
    {
        return $this->belongsTo(MeteorBullet::class, 'meteor_bullet_id');
    }

    public function effect()
    {
        return $this->belongsTo(MeteorEffect::class, 'meteor_effect_id');
    }

    public function attempts()
    {
        return $this->hasMany(MeteorGameAttempt::class);
    }

    /**
     * Seluruh pengaturan JILID ini dalam bentuk yang langsung dipakai game di browser.
     * Semua nilai punya cadangan supaya JILID yang masternya terhapus tetap bisa dimainkan.
     */
    public function gameConfig(): array
    {
        $boss = $this->boss;
        $bullet = $this->bullet;

        return [
            'number' => (int) $this->level_number,
            'label' => $this->display_label,
            'keys' => $this->keyList(),
            'lives' => (int) $this->lives,
            'waveTarget' => (int) $this->wave_target,
            'spawnMs' => (int) $this->spawn_interval_ms,
            'fallSeconds' => (float) $this->fall_seconds,
            'bulletSeconds' => (float) $this->bullet_seconds,
            'bossShotGap' => (float) $this->boss_shot_gap,
            'bullets' => (int) $this->boss_bullets_per_shot,
            'bossHp' => (int) $this->boss_hp,
            'returns' => (bool) $this->bullet_returns,

            'bossName' => $boss?->name ?? 'Boss',
            'bossSprite' => $boss?->sprite ?? 'pocong',
            'bossHue' => (int) ($boss?->hue ?? 18),
            'bossWeapon' => $bullet?->name ?? 'Peluru',
            'bulletColors' => [
                $bullet?->color_core ?? '#ffdca6',
                $bullet?->color_mid ?? '#ff8b3d',
                $bullet?->color_edge ?? '#8b2b07',
            ],

            'theme' => $this->themeConfig(),
            'meteorEffect' => self::effectConfig($this->effect),
            'bulletEffect' => self::effectConfig($bullet?->effect ?? $this->effect),
        ];
    }

    /** Huruf unik, huruf kecil, dan tidak pernah kosong. */
    public function keyList(): array
    {
        $keys = array_unique(str_split(strtolower(trim((string) $this->allowed_keys))));
        $keys = array_values(array_filter($keys, fn ($c) => $c !== ' '));

        return $keys ?: str_split('asdfghjkl;');
    }

    private function themeConfig(): array
    {
        $t = $this->theme;

        return [
            'skyTop' => $t?->sky_top ?? '#04061a',
            'skyMid' => $t?->sky_mid ?? '#101a45',
            'skyBottom' => $t?->sky_bottom ?? '#1d2764',
            'groundTop' => $t?->ground_top ?? '#16371f',
            'groundBottom' => $t?->ground_bottom ?? '#040c07',
            'accent' => $t?->accent ?? '#6ec8ff',
            'wall' => $t?->wall_color ?? '#04150c',
            'dome' => $t?->dome_color ?? '#04150c',
            'skyObject' => $t?->sky_object ?? 'bintang',
            'isDark' => (bool) ($t?->is_dark ?? true),
        ];
    }

    private static function effectConfig(?MeteorEffect $e): array
    {
        return [
            'particles' => (int) ($e?->particle_count ?? 20),
            'spread' => (int) ($e?->particle_spread ?? 150),
            'hue' => (int) ($e?->particle_hue ?? 18),
            'shake' => (int) ($e?->shake_strength ?? 3),
            'beam' => $e?->beam_color ?? '#8ce6ff',
        ];
    }
}
