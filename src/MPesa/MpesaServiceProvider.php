<?php

namespace SmoDav\MPesa;

use Illuminate\Support\ServiceProvider;

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
        $this->app->bind('SmoDav\MPesa\Contracts\ConfigurationStore', 'SmoDav\MPesa\LaravelConfig');
        $this->app->singleton('flash', function () {
            return $this->app->make('SmoDav\Flash\Notifier');
        });
    }
}
