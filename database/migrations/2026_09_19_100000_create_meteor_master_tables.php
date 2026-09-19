<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master Game 10 Jari — bagian yang dulu keras di dalam kode JS sekarang jadi data
 * supaya admin bisa meramu sendiri tingkat kesulitannya.
 *
 * - meteor_themes  : "Nuansa" (warna langit, tanah, markas)
 * - meteor_effects : "Efek" (ledakan & getaran saat sesuatu dihancurkan)
 * - meteor_bullets : "Peluru" (warna + efek saat kena) — namanya sekaligus nama senjata boss
 * - meteor_bosses  : "Boss" (nama + gambar mana yang dipakai)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meteor_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('sky_top', 7)->default('#04061a');
            $table->string('sky_mid', 7)->default('#101a45');
            $table->string('sky_bottom', 7)->default('#1d2764');
            $table->string('ground_top', 7)->default('#16371f');
            $table->string('ground_bottom', 7)->default('#040c07');
            $table->string('accent', 7)->default('#6ec8ff');
            $table->string('wall_color', 7)->default('#04150c');
            $table->string('dome_color', 7)->default('#04150c');
            $table->string('sky_object')->default('bintang');   // bintang | awan | tidak_ada
            $table->boolean('is_dark')->default(true);
            $table->timestamps();
        });

        Schema::create('meteor_effects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedTinyInteger('particle_count')->default(20);
            $table->unsignedSmallInteger('particle_spread')->default(150);
            $table->unsignedSmallInteger('particle_hue')->default(18);
            $table->unsignedTinyInteger('shake_strength')->default(3);
            $table->string('beam_color', 7)->default('#8ce6ff');
            $table->timestamps();
        });

        Schema::create('meteor_bullets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color_core', 7)->default('#ffdca6');
            $table->string('color_mid', 7)->default('#ff8b3d');
            $table->string('color_edge', 7)->default('#8b2b07');
            $table->foreignId('meteor_effect_id')->nullable()->constrained('meteor_effects')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('meteor_bosses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('sprite')->default('pocong');
            $table->unsignedSmallInteger('hue')->default(18);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meteor_bullets');
        Schema::dropIfExists('meteor_bosses');
        Schema::dropIfExists('meteor_effects');
        Schema::dropIfExists('meteor_themes');
    }
};
