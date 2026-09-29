<?php

namespace Vestra\Ghasedak;

use Illuminate\Support\ServiceProvider;
use Vestra\Ghasedak\Services\GhasedakSms;

class GhasedakServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ghasedak.php', 'ghasedak');

        $this->app->singleton(GhasedakSms::class, function ($app) {
            return new GhasedakSms(
                apiKey: config('ghasedak.api_key'),
                baseUrl: config('ghasedak.base_url', 'http://api.ghasedaksms.com/v2'),
                timeout: (int) config('ghasedak.timeout', 10),
                connectTimeout: (int) config('ghasedak.connect_timeout', 5),
                templates: (array) config('ghasedak.templates', []),
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/ghasedak.php' => config_path('ghasedak.php'),
        ], 'ghasedak-config');
    }
}
