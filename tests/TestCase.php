<?php

namespace SmoDav\Mpesa\Tests;

use Mockery;
use GuzzleHttp\Psr7;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase as PHPUnit;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Native\NativeCache;
use SmoDav\Mpesa\Native\NativeConfig;

class TestCase extends PHPUnit
{
    /**
     * Engine Core.
     *
     * @var Engine
     **/
    protected $engine;

    /**
     * Set mocks.
     **/
    public function setUp()
    {
        $client  = Mockery::mock(ClientInterface::class);
        $promise = new Psr7\Response();
        $client->shouldReceive('request')->andReturn($promise);
        $config       = new NativeConfig();
        $cache        = new NativeCache($config);
        $this->engine = new Core($client, $config, $cache);
    }
}
