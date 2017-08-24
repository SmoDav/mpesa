<?php

namespace SmoDav\Mpesa\Tests\Unit;

use GuzzleHttp\Client;
use SmoDav\Mpesa\Tests\TestCase;
use SmoDav\Mpesa\C2B\Identity;

class IdentityTest extends TestCase
{
    /**
     * Test that authenticator works.
     *
     * @test
     * @expectedException     \SmoDav\Mpesa\Exceptions\ErrorException
     **/
    public function testValidationOfIdentity()
    {
        $this->engine->setClient(new Client());
        $auth = new Identity($this->engine);
        $response = $auth->validate('254700000000');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
