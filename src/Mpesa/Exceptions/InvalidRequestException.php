<?php
namespace SmoDav\Mpesa\Exceptions;

use Exception;

/**
 * Class InvalidRequestException
 *
 * @category PHP
 * @package  SmoDav\Mpesa\Exceptions
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class InvalidRequestException extends Exception
{
    /**
     * Error messages with their corresponding keys
     *
     * @var array
     */
    const ERRORS = [
        'VA_PAYBILL' => 'The Paybill Number is required',
        'VA_PASSWORD' => 'The Password is required',
        'VA_TIMESTAMP' => 'The Timestamp is required',
        'VA_TRANS_ID' => 'The Transaction ID is required',
        'VA_REF_ID' => 'The Reference ID is required',
        'VA_AMOUNT' => 'The Transaction Amount is required',
        'VA_NUMBER' => 'The Mobile Number is required',
        'VA_CALL_URL' => 'The Callback URL is required',
        'VA_CALL_METHOD' => 'The Callback Method is required',
    ];
}
