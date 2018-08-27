<?php

namespace SmoDav\Mpesa\Traits;

use SmoDav\Mpesa\Engine\Core;

trait MakesRequest
{
    /**
     * Initiate the request.
     *
     * @param array  $body
     * @param string $endpoint
     * @param string $account
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function makeRequest($body, $endpoint, $account = null)
    {
        return Core::instance()->client()
            ->request(
                'POST',
                $endpoint,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . Core::instance()->auth()->authenticate($account),
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => $body,
                ]
            );
    }

    /**
     * Get the password for the
     *
     * @param string $shortCode
     * @param string $passkey
     * @param string $time
     *
     * @return string
     */
    private function getPassword($shortCode, $passkey, $time)
    {
        return base64_encode($shortCode . $passkey . $time);
    }
}
