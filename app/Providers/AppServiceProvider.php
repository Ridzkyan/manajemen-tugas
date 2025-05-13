<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('id'); // 🟢 Aktifkan bahasa Indonesia
        setlocale(LC_TIME, 'id_ID.UTF-8'); // 🟢 Untuk format tanggal lokal
    }
}
