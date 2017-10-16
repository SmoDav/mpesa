<?php

namespace SmoDav\Mpesa\Tests\Unit;

use SmoDav\Mpesa\Tests\TestCase;
use GuzzleHttp\ClientInterface;
use SmoDav\Mpesa\Auth\Authenticator;
use SmoDav\Mpesa\Contracts\CacheStore;
use SmoDav\Mpesa\Contracts\ConfigurationStore;

class CoreTest extends TestCase
{
    /**
     * Test that the authenticator is set.
     *
     * @test
     **/
    public function testAuthSet()
    {
        $this->assertInstanceOf(Authenticator::class, $this->engine->auth);
    }

    /**
     * Test that the http client is set.
     *
     * @test
     **/
    public function testClientSet()
    {
        $this->assertInstanceOf(ClientInterface::class, $this->engine->client);
    }

    /**
     * Test that the configuration store is set.
     *
     * @test
     **/
    public function testConfigStoreSet()
    {
        $this->assertInstanceOf(ConfigurationStore::class, $this->engine->config);
    }

    /**
     * Test that the cache store is set.
     *
     * @test
     **/
    public function testCacheSet()
    {
        $this->assertInstanceOf(CacheStore::class, $this->engine->cache);
    }
}
