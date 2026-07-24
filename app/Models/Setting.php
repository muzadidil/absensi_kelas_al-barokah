<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Pengaturan situs (branding) berbasis key-value.
 *
 * Semua pembacaan lewat cache 'app_settings' (satu query untuk semua key),
 * di-flush otomatis setiap kali ada perubahan lewat put(). Semua accessor
 * dibungkus try/catch supaya halaman tetap tampil (pakai default) meski
 * tabel belum termigrasi di lingkungan baru.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Ambil nilai satu pengaturan (atau default kalau belum diset).
     */
    public static function get(string $key, $default = null)
    {
        return static::all_cached()->get($key, $default);
    }

    /**
     * Simpan/replace satu pengaturan, lalu buang cache.
     */
    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('app_settings');
    }

    /**
     * Semua pengaturan sebagai koleksi key => value (di-cache).
     */
    protected static function all_cached()
    {
        try {
            return Cache::rememberForever('app_settings', function () {
                return static::pluck('value', 'key');
            });
        } catch (\Throwable $e) {
            // Tabel belum ada / DB belum siap — kembalikan koleksi kosong
            // supaya semua accessor jatuh ke nilai default.
            return collect();
        }
    }

    // ---- Accessor siap-pakai untuk layout (selalu mengembalikan nilai valid) ----

    public static function faviconUrl(): string
    {
        $path = static::get('favicon_path');

        return $path ? asset($path) : asset('favicon.ico');
    }

    public static function loginLogoUrl(): string
    {
        $path = static::get('login_logo_path');

        return $path ? asset($path) : asset('images/developer.png');
    }

    public static function address(): ?string
    {
        return static::get('address');
    }

    public static function siteName(): string
    {
        return static::get('site_name') ?: 'Sistem Absensi Kelas Al-Barokah';
    }
}
