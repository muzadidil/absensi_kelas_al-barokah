<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeteorBoss;
use App\Models\MeteorBullet;
use App\Models\MeteorEffect;
use App\Models\MeteorGameLevel;
use App\Models\MeteorTheme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Pengaturan Game 10 Jari. Satu halaman, lima tab: JILID + empat master.
 * Menghapus master tidak menghapus JILID-nya — kaitannya cuma dilepas
 * (nullOnDelete) dan game memakai nilai cadangan, lihat MeteorGameLevel::gameConfig().
 */
class MeteorGameController extends Controller
{
    private const HEX = ['required', 'regex:/^#[0-9a-fA-F]{6}$/'];

    public function index()
    {
        return view('admin.meteor.index', [
            'levels' => MeteorGameLevel::with(['theme', 'boss', 'bullet', 'effect'])
                ->orderBy('level_number')->get(),
            'themes' => MeteorTheme::orderBy('name')->get(),
            'effects' => MeteorEffect::orderBy('name')->get(),
            'bullets' => MeteorBullet::with('effect')->orderBy('name')->get(),
            'bosses' => MeteorBoss::orderBy('name')->get(),
            'sprites' => MeteorBoss::SPRITES,
            'skyObjects' => MeteorTheme::SKY_OBJECTS,
        ]);
    }

    // ---------- JILID ----------

    public function storeLevel(Request $request)
    {
        MeteorGameLevel::create($this->levelData($request));

        return back()->with('success', 'JILID berhasil ditambahkan.');
    }

    public function updateLevel(Request $request, MeteorGameLevel $meteorGameLevel)
    {
        $meteorGameLevel->update($this->levelData($request, $meteorGameLevel->id));

        return back()->with('success', 'JILID berhasil diperbarui.');
    }

    public function destroyLevel(MeteorGameLevel $meteorGameLevel)
    {
        $label = $meteorGameLevel->display_label;
        $meteorGameLevel->delete();

        return back()->with('success', "{$label} dihapus beserta riwayat percobaannya.");
    }

    private function levelData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'level_number' => ['required', 'integer', 'min:1', 'max:999',
                Rule::unique('meteor_game_levels', 'level_number')->ignore($ignoreId)],
            'display_label' => 'required|string|max:255',
            'meteor_theme_id' => 'nullable|exists:meteor_themes,id',
            'meteor_boss_id' => 'nullable|exists:meteor_bosses,id',
            'meteor_bullet_id' => 'nullable|exists:meteor_bullets,id',
            'meteor_effect_id' => 'nullable|exists:meteor_effects,id',
            'allowed_keys' => 'required|string|max:60',
            'lives' => 'required|integer|min:1|max:20',
            'wave_target' => 'required|integer|min:1|max:500',
            'spawn_interval_ms' => 'required|integer|min:200|max:10000',
            'fall_seconds' => 'required|numeric|min:1|max:60',
            'bullet_seconds' => 'required|numeric|min:1|max:60',
            'boss_shot_gap' => 'required|numeric|min:0.5|max:30',
            'boss_bullets_per_shot' => 'required|integer|min:1|max:20',
            'boss_hp' => 'required|integer|min:1|max:500',
        ], [], [
            'allowed_keys' => 'huruf yang dipakai',
            'spawn_interval_ms' => 'jeda antar meteor',
            'fall_seconds' => 'lama meteor jatuh',
        ]);

        // huruf dirapikan di sini supaya tidak ada huruf dobel atau spasi nyasar
        $data['allowed_keys'] = implode('', array_unique(str_split(
            preg_replace('/\s+/', '', strtolower($data['allowed_keys']))
        )));
        $data['bullet_returns'] = $request->boolean('bullet_returns');
        $data['is_checkpoint'] = $request->boolean('is_checkpoint');

        return $data;
    }

    // ---------- Nuansa ----------

    public function storeTheme(Request $request)
    {
        MeteorTheme::create($this->themeData($request));

        return back()->with('success', 'Nuansa berhasil ditambahkan.');
    }

    public function updateTheme(Request $request, MeteorTheme $meteorTheme)
    {
        $meteorTheme->update($this->themeData($request, $meteorTheme->id));

        return back()->with('success', 'Nuansa berhasil diperbarui.');
    }

    public function destroyTheme(MeteorTheme $meteorTheme)
    {
        $meteorTheme->delete();

        return back()->with('success', 'Nuansa dihapus. JILID yang memakainya kembali ke tampilan bawaan.');
    }

    private function themeData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255',
                Rule::unique('meteor_themes', 'name')->ignore($ignoreId)],
            'sky_top' => self::HEX,
            'sky_mid' => self::HEX,
            'sky_bottom' => self::HEX,
            'ground_top' => self::HEX,
            'ground_bottom' => self::HEX,
            'accent' => self::HEX,
            'wall_color' => self::HEX,
            'dome_color' => self::HEX,
            'sky_object' => ['required', Rule::in(array_keys(MeteorTheme::SKY_OBJECTS))],
        ]);

        $data['is_dark'] = $request->boolean('is_dark');

        return $data;
    }

    // ---------- Efek ----------

    public function storeEffect(Request $request)
    {
        MeteorEffect::create($this->effectData($request));

        return back()->with('success', 'Efek berhasil ditambahkan.');
    }

    public function updateEffect(Request $request, MeteorEffect $meteorEffect)
    {
        $meteorEffect->update($this->effectData($request, $meteorEffect->id));

        return back()->with('success', 'Efek berhasil diperbarui.');
    }

    public function destroyEffect(MeteorEffect $meteorEffect)
    {
        $meteorEffect->delete();

        return back()->with('success', 'Efek dihapus. Yang memakainya kembali ke efek bawaan.');
    }

    private function effectData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255',
                Rule::unique('meteor_effects', 'name')->ignore($ignoreId)],
            'particle_count' => 'required|integer|min:0|max:120',
            'particle_spread' => 'required|integer|min:10|max:600',
            'particle_hue' => 'required|integer|min:0|max:360',
            'shake_strength' => 'required|integer|min:0|max:30',
            'beam_color' => self::HEX,
        ]);
    }

    // ---------- Peluru ----------

    public function storeBullet(Request $request)
    {
        MeteorBullet::create($this->bulletData($request));

        return back()->with('success', 'Peluru berhasil ditambahkan.');
    }

    public function updateBullet(Request $request, MeteorBullet $meteorBullet)
    {
        $meteorBullet->update($this->bulletData($request, $meteorBullet->id));

        return back()->with('success', 'Peluru berhasil diperbarui.');
    }

    public function destroyBullet(MeteorBullet $meteorBullet)
    {
        $meteorBullet->delete();

        return back()->with('success', 'Peluru dihapus. JILID yang memakainya kembali ke peluru bawaan.');
    }

    private function bulletData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255',
                Rule::unique('meteor_bullets', 'name')->ignore($ignoreId)],
            'color_core' => self::HEX,
            'color_mid' => self::HEX,
            'color_edge' => self::HEX,
            'meteor_effect_id' => 'nullable|exists:meteor_effects,id',
        ]);
    }

    // ---------- Boss ----------

    public function storeBoss(Request $request)
    {
        MeteorBoss::create($this->bossData($request));

        return back()->with('success', 'Boss berhasil ditambahkan.');
    }

    public function updateBoss(Request $request, MeteorBoss $meteorBoss)
    {
        $meteorBoss->update($this->bossData($request, $meteorBoss->id));

        return back()->with('success', 'Boss berhasil diperbarui.');
    }

    public function destroyBoss(MeteorBoss $meteorBoss)
    {
        $meteorBoss->delete();

        return back()->with('success', 'Boss dihapus. JILID yang memakainya kembali ke boss bawaan.');
    }

    private function bossData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255',
                Rule::unique('meteor_bosses', 'name')->ignore($ignoreId)],
            'sprite' => ['required', Rule::in(array_keys(MeteorBoss::SPRITES))],
            'hue' => 'required|integer|min:0|max:360',
        ]);
    }
}
