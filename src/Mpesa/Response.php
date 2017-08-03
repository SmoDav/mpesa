<?php

namespace SmoDav\Mpesa;

use GuzzleHttp\Psr7\Response as GuzzleResponse;

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
     * @var GuzzleResponse
     */
    public $response;

    /**
     * MpesaResponse constructor.
     *
     * @param $transactionId
     * @param GuzzleResponse | \stdClass $response
     */
    public function __construct($transactionId, $response)
    {
        $this->transactionId = $transactionId;
        $this->response      = $response;
    }
}
