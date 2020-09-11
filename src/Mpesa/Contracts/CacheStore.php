<?php

namespace SmoDav\Mpesa\Contracts;

/**
 * Interface CacheStore
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
interface CacheStore
{
    /**
     * Get the cache value from the store or a default value to be supplied.
     *
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Store an item in the cache.
     *
     * @param string                                     $key
     * @param mixed                                      $value
     * @param \DateTimeInterface|\DateInterval|float|int $seconds
     */
    public function put($key, $value, $seconds = null);

    /**
     * Get the cache or default value from the store and delete it.
     *
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    public function pull($key, $default = null);
}
