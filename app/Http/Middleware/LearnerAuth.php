<?php

namespace App\Http\Middleware;

use App\Models\Learner;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LearnerAuth
{
    /**
     * Pastikan session punya learner_id yang valid sebelum mengakses
     * halaman murid. Ini terpisah dari Auth::user() Laravel biasa,
     * karena murid login lewat data tabel `learners`, bukan `users`.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $learnerId = $request->session()->get('learner_id');

        if (! $learnerId || ! Learner::where('id', $learnerId)->exists()) {
            return redirect()->route('login')->withErrors([
                'pin' => 'Sesi Anda telah berakhir. Silakan masuk kembali.',
            ]);
        }

        return $next($request);
    }
}
