<?php

namespace SmoDav\Mpesa;

use GuzzleHttp\Psr7\Stream;

/**
 * Class MpesaResponse.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class Response
{
    /**
     * @var int
     */
    public $transactionId;
    /**
     * @var Stream
     */
    public $response;

    /**
     * MpesaResponse constructor.
     *
     * @param $transactionId
     * @param $response
     */
    public function __construct($transactionId, Stream $response)
    {
        $this->transactionId = $transactionId;
        $this->response      = $response;
    }
}
