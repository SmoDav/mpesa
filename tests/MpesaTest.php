<?php

use Mockery as mocker;
use SmoDav\Mpesa\Cashier;
use SmoDav\Mpesa\MpesaRepository;
use SmoDav\Mpesa\Transactor;

class MpesaTest extends PHPUnit_Framework_TestCase {

    protected $transactionGenerator;

    protected $cashier;

    protected $transactor;

    protected $store;

    public function setUp()
    {
        $this->transactionGenerator = mocker::mock('SmoDav\Mpesa\Contracts\Transactable');
        $this->store = mocker::mock('SmoDav\Mpesa\Contracts\ConfigurationStore');
        $this->transactor = mocker::mock('SmoDav\Mpesa\Transactor');
    }

    /** @test */
    public function it_should_fetch_configs_from_store()
    {
        $this->store->shouldReceive('get')->with('mpesa.demo');
        $this->store->shouldReceive('get')->with('mpesa.endpoint');
        $this->store->shouldReceive('get')->with('mpesa.callback_url');
        $this->store->shouldReceive('get')->with('mpesa.callback_method');
        $this->store->shouldReceive('get')->with('mpesa.paybill_number');
        $this->store->shouldReceive('get')->with('mpesa.passkey');
        $this->store->shouldReceive('get')->with('mpesa.transaction_id_handler');
        $this->store->shouldReceive('get')->with('mpesa.transaction_id_handler');

        new MpesaRepository($this->store);
    }

    /** @test */
    public function it_throws_exception_on_invalid_amount()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->cashier = new Cashier($this->transactor);

        $this->cashier->request('twenty');
    }

    /** @test */
    public function it_throws_exception_on_invalid_subscriber_number()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->cashier = new Cashier($this->transactor);

        $this->cashier->from(0722000000);
    }

    /** @test */
    public function it_throws_exception_on_invalid_reference_id()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->cashier = new Cashier($this->transactor);

        $this->cashier->usingReferenceId('credit');
    }

    /** @test */
    public function it_should_call_the_transactor()
    {
        $this->transactor->shouldReceive('process')->with(20, 254722000000, 154452);

        $this->cashier = new Cashier($this->transactor);

        $this->cashier->request(20)->from(254722000000)->usingReferenceId(154452)->transact();
    }
}
