<?php

namespace SmoDav\Mpesa\C2B;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use SmoDav\Mpesa\Auth\Authenticator;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Repositories\EndpointsRepository;

class Identity
{
    protected $engine;
    protected $authenticator;
    protected $endpoint;

    /**
     * Identity constructor.
     *
     * @param Core $engine
     * @param Authenticator $authenticator
     */
    public function __construct(Core $engine, Authenticator $authenticator)
    {
        $this->engine = $engine;
        $this->authenticator = $authenticator;
        $this->endpoint = EndpointsRepository::build(MPESA_ID_CHECK);
    }

    public function validate($number, $callback = null)
    {
        if (! starts_with($number, '2547')) {
            throw new \InvalidArgumentException('The subscriber number must start with 2547');
        }

        $time = Carbon::now()->format('YmdHis');
        $shortCode = $this->engine->config->get('mpesa.short_code');
        $passkey = $this->engine->config->get('mpesa.passkey');
        $defaultCallback = $this->engine->config->get('mpesa.id_validation_callback');
        $initiator = $this->engine->config->get('mpesa.initiator');
        $password = base64_encode($shortCode . ':' . $passkey . ':' . $time);

        $body = [
            //Fill in the request parameters with valid values
            'Initiator' => $initiator,
            'BusinessShortCode' => $shortCode,
            'Password' => $password,
            'Timestamp' => $time,
            'TransactionType' => 'CheckIdentity',
            'PhoneNumber' => $number,
            'CallBackURL' => $callback ?: $defaultCallback,
            'TransactionDesc' => ' '
        ];

        try {
            $response = $this->makeRequest($body);

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
        }
    }

    /**
     * Initiate the request.
     *
     * @param array $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function makeRequest($body = [])
    {
        return $this->engine->client->request('POST', $this->endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->authenticator->authenticate(),
                'Content-Type' => 'application/json',
            ],
            'json' => $body,
        ]);
    }
}
