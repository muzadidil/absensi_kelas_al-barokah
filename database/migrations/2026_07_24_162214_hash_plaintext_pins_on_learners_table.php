<?php

use App\Models\Learner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * PIN sebelumnya disimpan plaintext (4 digit angka). Sejak sekarang PIN
     * di-hash (bcrypt, selalu 60 karakter) — migration ini mengonversi data
     * lama yang masih plaintext, sekali jalan. Aman dijalankan berulang:
     * PIN yang sudah berbentuk hash bcrypt (60 karakter) dilewati.
     */
    public function up(): void
    {
        Learner::whereNotNull('pin')
            ->get()
            ->each(function (Learner $learner) {
                if (strlen($learner->pin) < 60) {
                    $learner->update(['pin' => Hash::make($learner->pin)]);
                }
            });
    }

    public function down(): void
    {
        // Tidak bisa mengembalikan PIN plaintext dari hash — no-op.
    }
};
