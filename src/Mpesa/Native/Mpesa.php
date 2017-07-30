<?php

namespace SmoDav\Mpesa\Native;

use SmoDav\Mpesa\Cashier;
use SmoDav\Mpesa\MpesaRepository;
use SmoDav\Mpesa\Transactor;
use Http\Adapter\GuzzleHttpAdapter;

class Mpesa extends Cashier
{
    /**
     * Mpesa constructor.
     */
    public function __construct()
    {
        $transactor = new Transactor(new MpesaRepository(new NativeConfig));

        parent::__construct($transactor);
    }
}
