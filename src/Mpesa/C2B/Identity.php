<?php

namespace SmoDav\Mpesa\C2B;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Repositories\ConfigurationRepository;
use SmoDav\Mpesa\Traits\MakesRequest;

/**
 * Class Identity.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 *
 * @method stdClass validate(string $number, callable $callback, string $account = null)
 */
class Identity
{
    use MakesRequest;

    /**
     * Prepare the number validation request
     *
     * @param int    $number
     * @param string $callback
     *
     * @return mixed
     */
    public function validate($number, $callback = null, $account = null)
    {
        if (! starts_with($number, '2547')) {
            throw new InvalidArgumentException('The subscriber number must start with 2547');
        }

        $time = Carbon::now()->format('YmdHis');
        $configs = (new ConfigurationRepository)->useAccount($account);

        $shortCode = $configs->getAccountKey('lnmo.shortcode');
        $passkey   = $configs->getAccountKey('lnmo.passkey');
        $callback  = $configs->getAccountKey('lnmo.callback');

        $defaultCallback = $configs->getAccountKey('id_validation_callback');
        $initiator = $configs->getAccountKey('initiator');

        $body = [
            'Initiator'         => $initiator,
            'BusinessShortCode' => $shortCode,
            'Password'          => $this->getPassword($shortCode, $passkey, $time),
            'Timestamp'         => $time,
            'TransactionType'   => 'CheckIdentity',
            'PhoneNumber'       => $number,
            'CallBackURL'       => $callback ?: $defaultCallback,
            'TransactionDesc'   => ' '
        ];

        try {
            $response = $this->makeRequest(
                $body,
                Core::instance()->getEndpoint(MPESA_ID_CHECK, $account),
                $account
            );

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
        }
    }
}
