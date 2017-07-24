<?php

namespace SmoDav\Mpesa;

use Http\Adapter\AdapterInterface;
use Http\Adapter\GuzzleHttpAdapter;
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
        // Let us register our http adapter
        $this->bind(AdapterInterface::class, function () {
            return new GuzzleHttpAdapter([
                                            'verify'          => false,
                                            'timeout'         => 60,
                                            'allow_redirects' => false,
                                            'expect'          => false,
                                        ]);
        });

        $this->app->bind('mpesa', function () {
            return $this->app->make(Cashier::class);
        });
    }
}
