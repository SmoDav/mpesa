<?php

namespace SmoDav\Mpesa\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use SmoDav\Mpesa\Exceptions\ErrorException;
use SmoDav\Mpesa\Tests\TestCase as TestCase;

class AuthenticatorTest extends TestCase
{
    /*
     * Test that authenticator works.
     *
     * @test
     **/
    public function testCanAuthenticateUsingRequestAndCached()
    {
        $mock = new MockHandler([
            new Response(202, [], json_encode(['access_token' => 'access', 'expires_in' => 3599])),
        ]);

        $core = $this->core(new Client(['handler' => HandlerStack::create($mock)]));

        $this->assertEquals('access', $core->auth()->authenticate());

        $mock = new MockHandler([
            new Response(403, [], json_encode(['access_token' => 'access', 'expires_in' => 3599])),
        ]);

        $core = $this->core(new Client(['handler' => HandlerStack::create($mock)]));

        $this->assertEquals('access', $core->auth()->authenticate());

        $mock = new MockHandler([
            new Response(403, [], json_encode(['access_token' => 'access', 'expires_in' => 600000])),
        ]);

        $core = $this->core(new Client(['handler' => HandlerStack::create($mock)]));

        $core->auth()->flushTokens();

        $this->expectException(ErrorException::class);

        $core->auth()->authenticate();
    }
}
