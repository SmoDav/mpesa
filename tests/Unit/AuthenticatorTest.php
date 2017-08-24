<?php

namespace SmoDav\Mpesa\Tests\Unit;

use GuzzleHttp\Client;
use SmoDav\Mpesa\Tests\TestCase;
use SmoDav\Mpesa\Auth\Authenticator;

class AuthenticatorTest extends TestCase
{
    /**
     * Test that authenticator works.
     *
     * @test
     * @expectedException     \SmoDav\Mpesa\Exceptions\ErrorException
     **/
    public function testAuthentication()
    {
        $this->engine->setClient(new Client());
        $auth = new Authenticator($this->engine);
        $auth->authenticate();
    }
}
