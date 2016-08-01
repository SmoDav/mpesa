<?php

namespace SmoDav\Mpesa\Native;

use SmoDav\Mpesa\Cashier;
use SmoDav\Mpesa\MpesaRepository;
use SmoDav\Mpesa\Transactor;

class Mpesa extends Cashier
{

    /**
     * Mpesa constructor.
     */
    public function __construct()
    {
        $config = new NativeConfig;
        $repository = new MpesaRepository($config);
        $transactor = new Transactor($repository);

        parent::__construct($transactor);
    }
}
