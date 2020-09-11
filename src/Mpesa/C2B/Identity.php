<?php

namespace SmoDav\Mpesa\C2B;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use SmoDav\Mpesa\Repositories\Endpoint;
use SmoDav\Mpesa\Traits\UsesCore;
use SmoDav\Mpesa\Traits\Validates;

class Identity
{
    use UsesCore, Validates;

    /**
     * Prepare the number validation request
     *
     * @param int    $number
     * @param string $callback
     *
     * @return mixed
     */
    public function validate($number, $callback = null)
    {
        $this->validateNumber($number);

        $time = Carbon::now()->format('YmdHis');

        $shortCode = $this->core->configRepository()->getAccountKey('lnmo.shortcode');
        $passkey   = $this->core->configRepository()->getAccountKey('lnmo.passkey');
        $callback  = $this->core->configRepository()->getAccountKey('lnmo.callback');

        $defaultCallback = $this->core->configRepository()->getAccountKey('id_validation_callback');
        $initiator = $this->core->configRepository()->getAccountKey('initiator');

        $body = [
            'Initiator'         => $initiator,
            'BusinessShortCode' => $shortCode,
            'Password'          => $this->password($shortCode, $passkey, $time),
            'Timestamp'         => $time,
            'TransactionType'   => 'CheckIdentity',
            'PhoneNumber'       => $number,
            'CallBackURL'       => $callback ?: $defaultCallback,
            'TransactionDesc'   => ' '
        ];

        try {
            $response = $this->clientRequest(
                $body,
                $this->core->configRepository()->url(Endpoint::MPESA_ID_CHECK)
            );

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
        }
    }
}
