<?php
/*
 *   This file is part of the Smodav Mpesa library.
 *
 *   Copyright (c) 2016 SmoDav
 *
 *   For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 */
namespace SmoDav\Mpesa;

use Http\Adapter\AdapterInterface;
use Http\Adapter\GuzzleHttpAdapter;
use Illuminate\Support\ServiceProvider;
use SmoDav\Mpesa\Contracts\ConfigurationStore;

class MpesaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/mpesa.php' => config_path('mpesa.php')
        ]);
    }

    /**
     * Register the application services.
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
