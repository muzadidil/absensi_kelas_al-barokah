<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Pengaturan Situs (branding) — hanya admin.
 * Kelola favicon, logo halaman login, nama situs, dan alamat.
 *
 * Gambar disimpan langsung di public/uploads/branding/ (tanpa perlu
 * `php artisan storage:link`) supaya tetap jalan di shared hosting.
 */
class SettingController extends Controller
{
    private const UPLOAD_DIR = 'uploads/branding';

    public function index()
    {
        return view('admin.settings.index', [
            'siteName'     => Setting::get('site_name', ''),
            'address'      => Setting::get('address', ''),
            'faviconPath'  => Setting::get('favicon_path'),
            'loginLogoPath' => Setting::get('login_logo_path'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'site_name'   => 'nullable|string|max:255',
            'address'     => 'nullable|string|max:1000',
            'favicon'     => 'nullable|image|mimes:png,jpg,jpeg,svg,ico,webp|max:1024',
            'login_logo'  => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ], [
            'favicon.max'    => 'Ukuran favicon maksimal 1 MB.',
            'login_logo.max' => 'Ukuran logo maksimal 2 MB.',
            'favicon.image'    => 'Favicon harus berupa gambar.',
            'login_logo.image' => 'Logo harus berupa gambar.',
        ]);

        Setting::put('site_name', $request->input('site_name'));
        Setting::put('address', $request->input('address'));

        if ($request->hasFile('favicon')) {
            Setting::put('favicon_path', $this->storeImage($request->file('favicon'), 'favicon', Setting::get('favicon_path')));
        }

        if ($request->hasFile('login_logo')) {
            Setting::put('login_logo_path', $this->storeImage($request->file('login_logo'), 'login-logo', Setting::get('login_logo_path')));
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan situs berhasil disimpan!');
    }

    /**
     * Pindahkan file ke public/uploads/branding, hapus file lama bila ada,
     * dan kembalikan path relatif (dari public/) untuk disimpan di DB.
     */
    private function storeImage(UploadedFile $file, string $prefix, ?string $oldPath): string
    {
        $dir = public_path(self::UPLOAD_DIR);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Hapus file lama agar tidak menumpuk.
        if ($oldPath && file_exists(public_path($oldPath))) {
            @unlink(public_path($oldPath));
        }

        $filename = $prefix . '-' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return self::UPLOAD_DIR . '/' . $filename;
    }
}
