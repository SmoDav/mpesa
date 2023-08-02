<?php

namespace SmoDav\Mpesa\C2B;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use SmoDav\Mpesa\Repositories\Endpoint;
use SmoDav\Mpesa\Traits\UsesCore;
use SmoDav\Mpesa\Traits\UsesSTKMethods;
use SmoDav\Mpesa\Traits\Validates;
use stdClass;

class STK
{
    use UsesCore;
    use Validates;
    use Macroable;
    use UsesSTKMethods;

    const CUSTOMER_BUYGOODS_ONLINE = 'CustomerBuyGoodsOnline';

    const CUSTOMER_PAYBILL_ONLINE = 'CustomerPayBillOnline';

    const VALID_COMMANDS = [
        self::CUSTOMER_BUYGOODS_ONLINE,
        self::CUSTOMER_PAYBILL_ONLINE,
    ];



    /**
     * The MPesa callback URL to be used for the request.
     *
     * @var string
     */
    protected $callback = null;

    /**
     * Set the callback on completion.
     *
     * @param string $callback
     *
     * @return self
     */
    public function setCallback(string $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Prepare the STK Push request.
     *
     * @param int|null    $amount
     * @param int|null    $number
     * @param string|null $reference
     * @param string|null $description
     * @param string|null $account
     * @param string|null $command
     *
     * @return mixed
     */
    public function push(
        $amount = null,
        $number = null,
        $reference = null,
        $description = null,
        $account = null,
        $command = null
    ) {
        $this->set($amount, $number, $command);

        $this->core->useAccount($account ?: $this->account);
        $time = Carbon::now()->format('YmdHis');

        $paybill = $this->core->configRepository()->getAccountKey('lnmo.paybill');
        $shortCode = $this->core->configRepository()->getAccountKey('lnmo.shortcode');
        $passkey = $this->core->configRepository()->getAccountKey('lnmo.passkey');
        $callback = $this->callback ?: $this->core->configRepository()->getAccountKey('lnmo.callback');

        $partyB = $this->command == self::CUSTOMER_PAYBILL_ONLINE ? $shortCode : $paybill;

        $body = [
            'BusinessShortCode' => $shortCode,
            'Password' => $this->password($shortCode, $passkey, $time),
            'Timestamp' => $time,
            'TransactionType' => $this->command,
            'Amount' => $this->amount,
            'PartyA' => $this->number,
            'PartyB' => $partyB,
            'PhoneNumber' => $number ?: $this->number,
            'CallBackURL' => $callback,
            'AccountReference' => $reference ?: $this->reference,
            'TransactionDesc' => $description ?: $this->description,
        ];

        try {
            $response = $this->clientRequest(
                $body,
                $this->core->configRepository()->url(Endpoint::MPESA_LNMO)
            );

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
        }
    }

    /**
     * Validate an initialized transaction.
     *
     * @param string     $checkoutRequestID
     * @param mixed|null $account
     *
     * @return stdClass
     */
    public function validate($checkoutRequestID, $account = null)
    {
        $this->core->useAccount($account ?: $this->account);
        $time = Carbon::now()->format('YmdHis');

        $shortCode = $this->core->configRepository()->getAccountKey('lnmo.shortcode');
        $passkey = $this->core->configRepository()->getAccountKey('lnmo.passkey');

        $body = [
            'BusinessShortCode' => $shortCode,
            'Password' => $this->password($shortCode, $passkey, $time),
            'Timestamp' => $time,
            'CheckoutRequestID' => $checkoutRequestID,
        ];

        try {
            $response = $this->clientRequest(
                $body,
                $this->core->configRepository()->url(Endpoint::MPESA_LNMO_VALIDATE)
            );

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
        }
    }
}
