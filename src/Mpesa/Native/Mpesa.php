<?php
/*
 *   This file is part of the Smodav Mpesa library.
 *
 *   Copyright (c) 2016 SmoDav
 *
 *   For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 */
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
