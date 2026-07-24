<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Learner;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
          Paginator::useBootstrap();

          // Murid login lewat session('learner_id'), bukan Auth::user(), jadi
          // layout siswa butuh $learner disuntik lewat composer supaya topbar
          // & sidebar selalu punya datanya tanpa tiap controller harus ingat
          // untuk mengirimkannya sendiri.
          View::composer('layouts.learner', function ($view) {
              $view->with('learner', Learner::find(session('learner_id')));
          });
    }
}
