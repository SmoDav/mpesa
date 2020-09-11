<?php

namespace SmoDav\Mpesa\Tests;

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase as PHPUnit;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Native\NativeConfig;
use SmoDav\Mpesa\Tests\Unit\NativeImplementationsTest;

class TestCase extends PHPUnit
{
    /**
     * Get the core instance.
     *
     * @param ClientInterface $client
     *
     * @return Core
     */
    protected function core(ClientInterface $client)
    {
        return new Core($client, new NativeConfig(NativeImplementationsTest::CONFIG_FILE));
    }
}
