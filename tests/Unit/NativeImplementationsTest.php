<?php

namespace SmoDav\Mpesa\Tests\Unit;

use InvalidArgumentException;
use SmoDav\Mpesa\Native\NativeCache;
use SmoDav\Mpesa\Native\NativeConfig;
use SmoDav\Mpesa\Tests\TestCase;

class NativeImplementationsTest extends TestCase
{
    const CONFIG_FILE = __DIR__ . '/../files/mpesa.php';
    const CACHE_LOCATION = __DIR__ . '/../files/cache';

    /**
     * @return void
     */
    public function testCanUseNativeConfig()
    {
        $config = new NativeConfig();
        $this->assertEquals(true, $config->get('mpesa.accounts.staging.sandbox'));

        $config = new NativeConfig(self::CONFIG_FILE);
        $this->assertEquals(true, $config->get('mpesa.accounts.test.sandbox'));
    }

    /**
     * @return void
     */
    public function testCanGetWholeConfiguration()
    {
        $config = new NativeConfig(self::CONFIG_FILE);

        $this->assertEquals(require(self::CONFIG_FILE), $config->get('mpesa'));
    }

    /**
     * @return void
     */
    public function testCanUseNativeCache()
    {
        $cache = new NativeCache(self::CACHE_LOCATION);

        $cache->put('test', 123, 10);
        $this->assertEquals(123, $cache->get('test'));
    }

    /**
     * @return void
     */
    public function testCanOverwriteCacheItem()
    {
        $cache = new NativeCache(self::CACHE_LOCATION);

        $cache->put('overwrite', 123, 10);
        $this->assertEquals(123, $cache->get('overwrite'));

        $cache->put('overwrite', 456, 10);
        $this->assertEquals(456, $cache->get('overwrite'));
    }

    /**
     * @return void
     */
    public function testCacheExpires()
    {
        $cache = new NativeCache(self::CACHE_LOCATION);

        $cache->put('expire', 123, 1);
        $this->assertEquals(123, $cache->get('expire'));
        sleep(1);
        $this->assertEquals(null, $cache->get('expire'));
    }

    /**
     * @return void
     */
    public function testCanConstructWithoutConfig()
    {
        $cache = new NativeCache;
        $cache->put('without_config', 'YES', 5);
        $this->assertEquals('YES', $cache->get('without_config'));
    }

    /**
     * @return void
     */
    public function testTTLImplementation()
    {
        $cache = new NativeCache(self::CACHE_LOCATION);

        $this->expectException(InvalidArgumentException::class);
        $cache->put('expire', 123, 'fake');
    }

    /**
     * @return void
     */
    public function testShouldPullCacheItem()
    {
        $cache = new NativeCache(self::CACHE_LOCATION);
        $cache->put('to_pull', 'YES', 5);
        $this->assertEquals('YES', $cache->get('to_pull'));
        $this->assertEquals('YES', $cache->pull('to_pull'));
        $this->assertEquals('pulled', $cache->pull('to_pull', 'pulled'));
    }
}
