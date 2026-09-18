<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Progres Game 10 Jari per murid (pola sama dengan quiz_reset_at).
 * - meteor_reset_at : diisi now() saat murid gagal, sehingga status lulus
 *   dihitung ulang HANYA dari percobaan setelah waktu ini.
 * - meteor_checkpoint_level : JILID checkpoint tertinggi yang pernah ditembus.
 *   Kolom ini sengaja TIDAK ikut direset — itulah gunanya checkpoint.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learners', function (Blueprint $table) {
            $table->timestamp('meteor_reset_at')->nullable()->after('quiz_reset_at');
            $table->unsignedSmallInteger('meteor_checkpoint_level')->default(0)->after('meteor_reset_at');
        });
    }

    public function down(): void
    {
        Schema::table('learners', function (Blueprint $table) {
            $table->dropColumn(['meteor_reset_at', 'meteor_checkpoint_level']);
        });
    }
};
