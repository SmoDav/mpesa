<?php

namespace SmoDav\Mpesa\Native;

use Carbon\Carbon;
use SmoDav\Mpesa\Contracts\CacheStore;

/**
 * Class NativeCache
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class NativeCache implements CacheStore
{
    /**
     * @var NativeConfig
     */
    private $config;

    /**
     * NativeCache constructor.
     *
     * @param NativeConfig $config
     */
    public function __construct(NativeConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Get the cache value.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $location = \trim($this->config->get('mpesa.cache_location')) . '/.mpc';

        if (! \is_file($location)) {
            return $default;
        }

        $cache = \unserialize(\file_get_contents($location));
        $cache = $this->cleanCache($cache, $location);

        if (! isset($cache[$key])) {
            return $default;
        }

        return $cache[$key]['v'];
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
        $directory = \trim($this->config->get('mpesa.cache_location'));
        $location  = $directory . '/.mpc';

        if (! \is_dir($directory)) {
            \mkdir($directory, 0755, true);
        }
        $initial = [];
        if (\is_file($location)) {
            $initial = \unserialize(\file_get_contents($location));
            $initial = $this->cleanCache($initial, $location);
        }

        $minutes = $minutes ? Carbon::now()->addMinutes($minutes)->toDateTimeString() : null;
        $payload = [$key => ['v' => $value, 't' => $minutes]];
        $payload = \serialize(\array_merge($payload, $initial));

        \file_put_contents($location, $payload);
    }

    private function cleanCache($initial, $location)
    {
        $initial = \array_filter($initial, function ($value) {
            if (! $value['t']) {
                return true;
            }

            if (Carbon::now()->gt(Carbon::parse($value['t']))) {
                return false;
            }

            return true;
        });

        \file_put_contents($location, \serialize($initial));

        return $initial;
    }
}
