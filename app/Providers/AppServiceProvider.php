<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use App\Models\User\Mahasiswa;

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
     *A
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8');

        // ✅ Override binding jika Laravel ada yang salah rujuk
        App::bind('App\Models\Mahasiswa', function () {
            return new Mahasiswa();
        });
    }
}
