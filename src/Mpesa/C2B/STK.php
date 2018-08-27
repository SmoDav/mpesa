<?php

namespace SmoDav\Mpesa\C2B;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Repositories\ConfigurationRepository;
use SmoDav\Mpesa\Traits\MakesRequest;

/**
 * Class STK.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 *
 * @method STK from(string $number)
 * @method STK request(string $amount)
 * @method stdClass push(string $amount = null, string $number = null, string $reference = null, string $description = null, string $account = null)
 * @method STK usingAccount(string $account)
 * @method STK usingReference(string $reference, string $description)
 * @method stdClass validate(string $checkoutRequestID, string $account = null)
 */
class STK
{
    use MakesRequest;

    /**
     * The mobile number
     *
     * @var string
     */
    protected $number;

    /**
     * The amount to request
     *
     * @var int
     */
    protected $amount;

    /**
     * The transaction reference
     *
     * @var string
     */
    protected $reference;

    /**
     * The transaction description
     *
     * @var string
     */
    protected $description;

    /**
     * The MPesa account to be used.
     *
     * @var string
     */
    protected $account = null;

    /**
     * Set the account to be used.
     *
     * @param string $account
     *
     * @return self
     */
    public function usingAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Set the request amount to be deducted.
     *
     * @param int $amount
     *
     * @return self
     */
    public function request($amount)
    {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException('The amount must be numeric');
        }

        $this->amount = $amount;

        return $this;
    }

    /**
     * Set the Mobile Subscriber Number to deduct the amount from.
     * Must be in format 2547XXXXXXXX.
     *
     * @param int $number
     *
     * @return self
     */
    public function from($number)
    {
        if (! starts_with($number, '2547')) {
            throw new InvalidArgumentException('The subscriber number must start with 2547');
        }

        $this->number = $number;

        return $this;
    }

    /**
     * Set the product reference number to bill the account.
     *
     * @param int    $reference
     * @param string $description
     *
     * @return self
     */
    public function usingReference($reference, $description)
    {
        $this->reference   = $reference;
        $this->description = $description;

        return $this;
    }

    /**
     * Prepare the STK Push request
     *
     * @param int    $amount
     * @param int    $number
     * @param string $reference
     * @param string $description
     * @param string $account
     *
     * @return mixed
     */
    public function push($amount = null, $number = null, $reference = null, $description = null, $account = null)
    {
        $account = $account ?: $this->account;
        $time = Carbon::now()->format('YmdHis');
        $configs = (new ConfigurationRepository)->useAccount($account);

        $shortCode = $configs->getAccountKey('lnmo.shortcode');
        $passkey   = $configs->getAccountKey('lnmo.passkey');
        $callback  = $configs->getAccountKey('lnmo.callback');

        $body = [
            'BusinessShortCode' => $shortCode,
            'Password'          => $this->getPassword($shortCode, $passkey, $time),
            'Timestamp'         => $time,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => $amount ?: $this->amount,
            'PartyA'            => $number ?: $this->number,
            'PartyB'            => $shortCode,
            'PhoneNumber'       => $number ?: $this->number,
            'CallBackURL'       => $callback,
            'AccountReference'  => $reference ?: $this->reference,
            'TransactionDesc'   => $description ?: $this->description,
        ];

        try {
            $response = $this->makeRequest(
                $body,
                Core::instance()->getEndpoint(MPESA_LNMO, $account),
                $account
            );

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
        }
    }

    /**
     * Validate an initialized transaction.
     *
     * @param string $checkoutRequestID
     *
     * @return json
     */
    public function validate($checkoutRequestID, $account = null)
    {
        $account = $account ?: $this->account;
        $time = Carbon::now()->format('YmdHis');
        $configs = (new ConfigurationRepository)->useAccount($account);

        $shortCode = $configs->getAccountKey('lnmo.shortcode');
        $passkey   = $configs->getAccountKey('lnmo.passkey');

        $body = [
            'BusinessShortCode' => $shortCode,
            'Password'          => $this->getPassword($shortCode, $passkey, $time),
            'Timestamp'         => $time,
            'CheckoutRequestID' => $checkoutRequestID,
        ];

        try {
            $response = $this->makeRequest(
                $body,
                Core::instance()->getEndpoint(MPESA_LNMO_VALIDATE, $account),
                $account
            );

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
        }
    }
}
