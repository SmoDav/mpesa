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
        $config     = new NativeConfig;
        $repository = new MpesaRepository($config);
        $client     = new GuzzleHttpAdapter([
                                            'verify'          => false,
                                            'timeout'         => 60,
                                            'allow_redirects' => false,
                                            'expect'          => false,
                                        ]);
        $transactor = new Transactor($repository, $client);

        parent::__construct($transactor);
    }
}
