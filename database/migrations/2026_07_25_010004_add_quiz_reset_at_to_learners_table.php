<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Penanda "reset pamungkas" per murid. Saat murid GAGAL di tahap ber-Mode
 * Pamungkas, kolom ini diisi now() → semua kunci menutup kembali (progres
 * lulus dihitung ulang HANYA dari percobaan setelah waktu reset ini).
 * Riwayat percobaan tetap utuh untuk hitung total kegigihan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learners', function (Blueprint $table) {
            $table->timestamp('quiz_reset_at')->nullable()->after('section');
        });
    }

    public function down(): void
    {
        Schema::table('learners', function (Blueprint $table) {
            $table->dropColumn('quiz_reset_at');
        });
    }
};
