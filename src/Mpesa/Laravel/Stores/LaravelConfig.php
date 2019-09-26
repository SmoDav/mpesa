<?php

namespace SmoDav\Mpesa\Laravel\Stores;

use Illuminate\Config\Repository;
use SmoDav\Mpesa\Contracts\ConfigurationStore;

class LaravelConfig implements ConfigurationStore
{
    /**
     * @var MpesaRepository
     */
    private $repository;

    /**
     * LaravelConfiguration constructor.
     *
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get given config value from the configuration store.
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->repository->get($key, $default);
    }
}
