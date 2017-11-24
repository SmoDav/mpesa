<?php

namespace SmoDav\Mpesa\Laravel\Stores;

use Illuminate\Cache\Repository;
use SmoDav\Mpesa\Contracts\CacheStore;

/**
 * Class LaravelCache
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
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
     * @param \DateTimeInterface|\DateInterval|float|int $minutes
     */
    public function put($key, $value, $minutes = null)
    {
        $this->repository->put($key, $value, $minutes);
    }
}
