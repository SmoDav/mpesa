<?php

namespace SmoDav\Mpesa\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use SmoDav\Mpesa\Auth\Authenticator;
use PHPUnit\Framework\TestCase;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Exceptions\ConfigurationException;
use SmoDav\Mpesa\Native\NativeCache;
use SmoDav\Mpesa\Native\NativeConfig;

class AuthenticatorTest extends TestCase
{
    protected $config;
    protected $cache;

    protected function setUp()
    {
        parent::setUp();
        $this->cleanCache();
        $this->config =new NativeConfig();
        $this->cache  = new NativeCache($this->config);
    }

    private function cleanCache()
    {
        $file = __DIR__ . '/../../cache/.mpc';
        if (\is_file($file)) {
            \unlink($file);
        }
    }

    /**
     * Test that authenticator works.
     *
     * @test
     **/
    public function testAuthentication()
    {
        $mock = new MockHandler([
            new Response(202, [], \json_encode(['access_token' => 'access', 'expires_in' => 3599])),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);
        $engine  = new Core($client, $this->config, $this->cache);
        $auth    = new Authenticator($engine);
        $token   = $auth->authenticate();
        $this->assertEquals('access', $token);
    }

    /**
     * Test that authenticator works.
     *
     * @test
     **/
    public function testAuthenticationFailure()
    {
        $this->expectException(ConfigurationException::class);
        $mock = new MockHandler([
            new Response(400, [], \json_encode([]), null, 'Bad Request: Invalid Credentials'),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);
        $engine  = new Core($client, $this->config, $this->cache);
        $auth    = new Authenticator($engine);
        $auth->authenticate();
    }
}
