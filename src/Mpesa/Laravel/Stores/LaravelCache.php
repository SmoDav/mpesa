<?php

namespace SmoDav\Mpesa\Laravel\Stores;

use Illuminate\Cache\Repository;
use SmoDav\Mpesa\Contracts\CacheStore;

class LaravelCache implements CacheStore
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

    /**
     * Store an item in the cache.
     *
     * @param string                                     $key
     * @param mixed                                      $value
     * @param \DateTimeInterface|\DateInterval|float|int $seconds
     */
    public function put($key, $value, $seconds = null)
    {
        $this->repository->put($key, $value, $seconds);
    }

    /**
     * Get the cache or default value from the store and delete it.
     *
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        return $this->repository->pull($key, $default);
    }
}
