<?php

namespace SmoDav\Mpesa\Laravel;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider as RootProvider;
use SmoDav\Mpesa\C2B\Identity;
use SmoDav\Mpesa\C2B\Registrar;
use SmoDav\Mpesa\C2B\Simulate;
use SmoDav\Mpesa\C2B\STK;
use SmoDav\Mpesa\Contracts\CacheStore;
use SmoDav\Mpesa\Contracts\ConfigurationStore;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Laravel\Stores\LaravelCache;
use SmoDav\Mpesa\Laravel\Stores\LaravelConfig;

class ServiceProvider extends RootProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../../config/mpesa.php' => config_path('mpesa.php')
        ]);
    }

    /**
     * Registrar the application services.
     */
    public function register()
    {
        $this->bindInstances();

        $this->registerFacades();
    }

    /**
     * Bind the MPesa Instances.
     *
     * @return void
     */
    private function bindInstances()
    {
        $this->app->bind(ConfigurationStore::class, LaravelConfig::class);
        $this->app->bind(CacheStore::class, LaravelCache::class);
        $this->app->singleton(Core::class, function ($app) {
            $config = $app->make(ConfigurationStore::class);
            $cache = $app->make(CacheStore::class);

            return new Core(new Client, $config, $cache);
        });
    }

    private function registerFacades()
    {
        $this->app->bind('mp_stk', function () {
            return $this->app->make(STK::class);
        });

        $this->app->bind('mp_registrar', function () {
            return $this->app->make(Registrar::class);
        });

        $this->app->bind('mp_identity', function () {
            return $this->app->make(Identity::class);
        });

        $this->app->bind('mp_simulate', function () {
            return $this->app->make(Simulate::class);
        });
    }
}
