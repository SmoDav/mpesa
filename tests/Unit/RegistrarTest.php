<?php

namespace SmoDav\Mpesa\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use SmoDav\Mpesa\C2B\Registrar;
use SmoDav\Mpesa\Tests\TestCase as TestCase;

class RegistrarTest extends TestCase
{
    /*
     * Test that authenticator works.
     *
     * @test
     **/
    public function testRegisterUrls()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['access_token' => 'access', 'expires_in' => 3599])),
            new Response(200, [], json_encode([
                'OriginatorConverstionID' => '123',
                'ConversationID' => '500',
                'ResponseDescription' => 'Success',
            ])),
        ]);

        $core = $this->core(new Client(['handler' => HandlerStack::create($mock)]));
        $core->auth()->flushTokens();

        $registrar = new Registrar($core);

        $response = $registrar->submit(123456, 'http://example.com', 'http://example.com');

        $this->assertEquals('123', $response->OriginatorConverstionID);
        $this->assertEquals('500', $response->ConversationID);
        $this->assertEquals('Success', $response->ResponseDescription);
    }
}
