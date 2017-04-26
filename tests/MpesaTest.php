<?php

use Mockery as mocker;
use PHPUnit\Framework\TestCase;
use SmoDav\Mpesa\Cashier;
use SmoDav\Mpesa\MpesaRepository;
use SmoDav\Mpesa\Native\NativeConfig;

class MpesaTest extends TestCase {

    protected $transactionGenerator;

    protected $cashier;

    protected $transactor;

    protected $store;

    protected $nativeStore;

    public function setUp()
    {
        $this->transactionGenerator = mocker::mock('SmoDav\Mpesa\Contracts\Transactable');
        $this->store = mocker::mock('SmoDav\Mpesa\Contracts\ConfigurationStore');
        $this->transactor = mocker::mock('SmoDav\Mpesa\Transactor');
        $this->nativeStore = new NativeConfig();
    }

    /** @test */
    public function it_throws_exception_on_invalid_amount()
    {
        $this->expectException('InvalidArgumentException');

        $this->cashier = new Cashier($this->transactor);

        $this->cashier->request('twenty');
    }

    /** @test */
    public function it_throws_exception_on_invalid_subscriber_number()
    {
        $this->expectException('InvalidArgumentException');

        $this->cashier = new Cashier($this->transactor);

        $this->cashier->from(0722000000);
    }
}
