<?php

namespace App\Providers;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Observers\SuratKeluarObserver;
use App\Observers\SuratMasukObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (
            // str_starts_with((string) config('app.url'), 'https://') ||
            request()->header('x-forwarded-proto') === 'https' ||
            request()->header('cf-visitor') === '{"scheme":"https"}'
        ) {
            URL::forceScheme('https');
        }

        SuratMasuk::observe(SuratMasukObserver::class);
        SuratKeluar::observe(SuratKeluarObserver::class);
    }
}
