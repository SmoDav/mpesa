<?php

namespace SmoDav\Mpesa\Traits;

use SmoDav\Mpesa\Engine\Core;

trait UsesCore
{
    /**
     * @var Core
     */
    protected $core;

    /**
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        $this->core = $core;
    }

    /**
     * Initiate the request.
     *
     * @param array  $body
     * @param string $endpoint
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function clientRequest($body, $endpoint)
    {
        return $this->core->client()->request('POST', $endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->bearer(),
                'Content-Type'  => 'application/json',
            ],
            'json' => $body,
        ]);
    }

    /**
     * Get the bearer token.
     *
     * @return string
     */
    protected function bearer()
    {
        return $this->core->auth()->authenticate();
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
    private function password($shortCode, $passkey, $time)
    {
        return base64_encode($shortCode . $passkey . $time);
    }
}
