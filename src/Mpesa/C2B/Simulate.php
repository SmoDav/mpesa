<?php

namespace SmoDav\Mpesa\C2B;

use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use SmoDav\Mpesa\Exceptions\ErrorException;
use SmoDav\Mpesa\Repositories\Endpoint;
use SmoDav\Mpesa\Traits\UsesCore;
use SmoDav\Mpesa\Traits\UsesSTKMethods;
use SmoDav\Mpesa\Traits\Validates;

class Simulate
{
    use UsesCore;
    use Validates;
    use UsesSTKMethods;

    /**
     * The transaction command to be used.
     *
     * @var string
     */
    protected $command = STK::CUSTOMER_PAYBILL_ONLINE;

    /**
     * Prepare the transaction simulation request
     *
     * @param int|null    $amount
     * @param int|null    $number
     * @param string|null $reference
     * @param string|null $account
     * @param string|null $command
     *
     * @throws ErrorException
     *
     * @return mixed
     */
    public function push(
        $amount = null,
        $number = null,
        $reference = null,
        $account = null,
        $command = null
    ) {
        $this->set($amount, $number, $command);
        $this->core->useAccount($account ?: $this->account);

        if (!$this->core->configRepository()->getAccountKey('sandbox')) {
            throw new ErrorException('Cannot simulate a transaction in the live environment.');
        }

        $shortCode = $this->core->configRepository()->getAccountKey('lnmo.shortcode');

        $body = [
            'CommandID'     => $this->command,
            'Amount'        => $this->amount,
            'Msisdn'        => $this->number,
            'ShortCode'     => $shortCode,
            'BillRefNumber' => $reference ?: $this->reference,
        ];

        try {
            $response = $this->clientRequest(
                $body,
                $this->core->configRepository()->url(Endpoint::MPESA_SIMULATE)
            );

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
        }
    }
}
