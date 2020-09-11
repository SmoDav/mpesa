<?php

namespace SmoDav\Mpesa\Native;

use Carbon\Carbon;
use DateInterval;
use DateTimeInterface;
use InvalidArgumentException;
use SmoDav\Mpesa\Contracts\CacheStore;
use SmoDav\Mpesa\Repositories\ConfigurationRepository;

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
     * @var string
     */
    private $cacheFile;

    /**
     * NativeCache constructor.
     *
     * @param string|null $cacheDirectory
     */
    public function __construct($cacheDirectory = null)
    {
        $this->setUp($cacheDirectory);
    }

    /**
     * Setup the cache file location.
     *
     * @param string $cacheDirectory
     *
     * @return void
     */
    private function setUp($cacheDirectory = null)
    {
        $cacheDirectory = $cacheDirectory
            ?: (new ConfigurationRepository(new NativeConfig))->config('cache_location');

        $cacheDirectory = rtrim($cacheDirectory, '/');

        if (! is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0755, true);
        }

        $this->cacheFile = $cacheDirectory . '/.mpc';

        if (!is_file($this->cacheFile)) {
            file_put_contents($this->cacheFile, serialize([]));
        }
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
        $cache = unserialize(file_get_contents($this->cacheFile));
        $cache = $this->cleanCache($cache, $this->cacheFile);

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
     * @param \DateTimeInterface|\DateInterval|float|int $seconds
     *
     * @return bool
     */
    public function put($key, $value, $seconds = null)
    {
        $initial = unserialize(file_get_contents($this->cacheFile));
        $initial = $this->cleanCache($initial, $this->cacheFile, false);

        $payload = [$key => ['v' => $value, 't' => $this->formatTimeFromSeconds($seconds)]];
        $payload = serialize(array_merge($initial, $payload));

        return file_put_contents($this->cacheFile, $payload) !== false;
    }

    /**
     * Get the seconds and format it.
     *
     * @param int|null $seconds
     *
     * @return string|null
     */
    private function formatTimeFromSeconds($seconds = null)
    {
        if (!$seconds) {
            return null;
        }

        if ($seconds instanceof DateTimeInterface || $seconds instanceof DateInterval) {
            return Carbon::parse($seconds)->toDateTimeString();
        }

        if (!is_numeric($seconds)) {
            throw new InvalidArgumentException('The seconds argument should be numeric');
        }

        return Carbon::now()->addSeconds($seconds)->toDateTimeString();
    }

    /**
     * Clean out the expired items
     *
     * @param array  $initial
     * @param string $location
     * @param bool   $save
     *
     * @return array
     */
    private function cleanCache($initial, $location, $save = true)
    {
        $initial = array_filter($initial, function ($value) {
            if (! $value['t']) {
                return true;
            }

            if (Carbon::now()->gt(Carbon::parse($value['t']))) {
                return false;
            }

            return true;
        });

        if ($save) {
            file_put_contents($location, serialize($initial));
        }

        return $initial;
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
        $cache = unserialize(file_get_contents($this->cacheFile));
        $cache = $this->cleanCache($cache, $this->cacheFile, false);

        if (! isset($cache[$key])) {
            file_put_contents($this->cacheFile, serialize($cache));

            return $default;
        }

        $value = $cache[$key]['v'];

        unset($cache[$key]);
        file_put_contents($this->cacheFile, serialize($cache));

        return $value;
    }
}
