<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        return view('guru.dashboard');
    }

    // Admin: list all users with the 'guru' role
    public function manage()
    {
        $gurus = User::whereHas('roles', fn ($q) => $q->where('name', 'guru'))
            ->orderBy('name')
            ->get();

        return view('admin.guru.index', compact('gurus'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Guru deleted successfully.');
    }
}
