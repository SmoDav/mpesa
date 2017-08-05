<?php

namespace SmoDav\Mpesa\C2B;

use GuzzleHttp\Exception\RequestException;
use SmoDav\Mpesa\Auth\Authenticator;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Repositories\EndpointsRepository;

class Register
{
    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var Core
     */
    private $engine;
    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * Register constructor.
     * @param Core $engine
     * @param Authenticator $authenticator
     */
    public function __construct(Core $engine, Authenticator $authenticator)
    {
        $this->engine = $engine;
        $this->authenticator = $authenticator;
        $this->endpoint = EndpointsRepository::build(MPESA_REGISTER);
    }

    public function submit($shortCode, $confirmationURL, $validationURL, $onTimeout = 'Completed')
    {
        $body = [
            'ShortCode' => $shortCode,
            'ResponseType' => $onTimeout,
            'ConfirmationURL' => $confirmationURL,
            'ValidationURL' => $validationURL
        ];

        try {
            $response = $this->makeRequest($body);

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            throw $this->generateException($exception->getResponse()->getReasonPhrase());
        }
    }

    /**
     * Initiate the registration request.
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

    private function generateException($getReasonPhrase)
    {
        return new \Exception($getReasonPhrase);
    }
}
