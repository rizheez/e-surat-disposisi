<?php

namespace App\Providers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Observers\SuratMasukObserver;
use App\Observers\SuratKeluarObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        SuratMasuk::observe(SuratMasukObserver::class);
        SuratKeluar::observe(SuratKeluarObserver::class);
    }
}
