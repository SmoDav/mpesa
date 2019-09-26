<?php

namespace SmoDav\Mpesa\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use SmoDav\Mpesa\C2B\STK;
use SmoDav\Mpesa\Tests\TestCase as TestCase;

class STKTest extends TestCase
{
    /*
     * Test that authenticator works.
     *
     * @test
     **/
    public function testPushRequest()
    {
        $response = [
            'MerchantRequestID' => '19465-780693-1',
            'CheckoutRequestID' => 'ws_CO_27072017154747416',
            'ResponseCode' => 0,
            'ResponseDescription' => 'Success. Request accepted for processing',
            'CustomerMessage' => 'Success. Request accepted for processing',
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode(['access_token' => 'access', 'expires_in' => 3599])),
            new Response(200, [], json_encode($response)),
        ]);

        $core = $this->core(new Client(['handler' => HandlerStack::create($mock)]));
        $core->auth()->flushTokens();

        $stk = new STK($core);

        $response = $stk->push(100, 254722000000, 'Test', 'Awesome');

        $this->assertEquals('19465-780693-1', $response->MerchantRequestID);
        $this->assertEquals('ws_CO_27072017154747416', $response->CheckoutRequestID);
        $this->assertEquals(0, $response->ResponseCode);
        $this->assertEquals('Success. Request accepted for processing', $response->ResponseDescription);
        $this->assertEquals('Success. Request accepted for processing', $response->CustomerMessage);
    }

    public function testValidateTransaction()
    {
        $response = [
            'MerchantRequestID' => '19465-780693-1',
            'CheckoutRequestID' => 'ws_CO_27072017154747416',
            'ResponseCode' => 0,
            'ResponseDescription' => 'Success. Request accepted for processing',
            'ResultCode' => 0,
            'ResultDesc' => 'The service request is processed successfully.'
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode(['access_token' => 'access', 'expires_in' => 3599])),
            new Response(200, [], json_encode($response)),
        ]);

        $core = $this->core(new Client(['handler' => HandlerStack::create($mock)]));
        $core->auth()->flushTokens();

        $stk = new STK($core);

        $response = $stk->validate('ws_CO_27072017154747416');

        $this->assertEquals('19465-780693-1', $response->MerchantRequestID);
        $this->assertEquals('ws_CO_27072017154747416', $response->CheckoutRequestID);
        $this->assertEquals(0, $response->ResponseCode);
        $this->assertEquals('Success. Request accepted for processing', $response->ResponseDescription);
        $this->assertEquals(0, $response->ResultCode);
        $this->assertEquals('The service request is processed successfully.', $response->ResultDesc);
    }
}
