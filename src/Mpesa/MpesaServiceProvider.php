<?php

namespace SmoDav\Mpesa;

use Illuminate\Support\ServiceProvider;
use SmoDav\Mpesa\Contracts\ConfigurationStore;

class MpesaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/mpesa.php' => config_path('mpesa.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ConfigurationStore::class, LaravelConfig::class);

        $this->app->singleton('MpesaRepository', function () {
            return $this->app->make(MpesaRepository::class);
        });

        $this->app->bind('mpesa', function () {
            return $this->app->make(Cashier::class);
        });
    }
}
