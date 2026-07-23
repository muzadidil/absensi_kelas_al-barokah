<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Seeder role + akun admin pertama. IDEMPOTENT — aman dijalankan
 * berulang (findOrCreate/firstOrCreate, tidak duplikat).
 *
 * Dibuat karena `/dashboard` abort(403) untuk user tanpa role, dan
 * `php artisan tinker` TIDAK bisa jalan di Hostinger shared hosting
 * (proc_open mati) — role produksi harus via seeder ini:
 *
 *   php artisan db:seed --class=RoleSeeder --force
 *
 * ⚠️ Ganti password akun admin segera setelah login pertama.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Rename legacy 'employee' role to 'guru' if it still exists (production data)
        $legacyEmployeeRole = Role::where('name', 'employee')->where('guard_name', 'web')->first();
        if ($legacyEmployeeRole) {
            $legacyEmployeeRole->update(['name' => 'guru']);
        }

        foreach (['admin', 'guru', 'learner'] as $role) {
            Role::findOrCreate($role, 'web');
        }

        $admin = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
