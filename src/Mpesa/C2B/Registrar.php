<?php

namespace SmoDav\Mpesa\C2B;

use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Repositories\EndpointsRepository;

/**
 * Class Registrar.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class Registrar
{
    /**
     * @var string
     */
    protected $endpoint;

    /**
     * The short code to register callbacks for.
     *
     * @var string
     */
    protected $shortCode;

    /**
     * The validation callback.
     *
     * @var
     */
    protected $validationURL;

    /**
     * The confirmation callback.
     *
     * @var
     */
    protected $confirmationURL;

    /**
     * The status of the request in case a timeout occurs.
     *
     * @var string
     */
    protected $onTimeout = 'Completed';

    /**
     * @var Core
     */
    private $engine;

    /**
     * Registrar constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine   = $engine;
        $this->endpoint = EndpointsRepository::build(MPESA_REGISTER);
    }

    /**
     * Submit the short code to be registered.
     *
     * @param $shortCode
     *
     * @return $this
     */
    public function register($shortCode)
    {
        $this->shortCode = $shortCode;

        return $this;
    }

    /**
     * Submit the callback to be used for validation.
     *
     * @param $validationURL
     *
     * @return $this
     */
    public function onValidation($validationURL)
    {
        $this->validationURL = $validationURL;

        return $this;
    }

    /**
     * Submit the callback to be used for confirmation.
     *
     * @param $confirmationURL
     *
     * @return $this
     */
    public function onConfirmation($confirmationURL)
    {
        $this->confirmationURL = $confirmationURL;

        return $this;
    }

    /**
     * Set the transaction status on timeout.
     *
     * @param string $onTimeout
     *
     * @return $this
     */
    public function onTimeout($onTimeout = 'Completed')
    {
        if ($onTimeout != 'Completed' && $onTimeout != 'Cancelled') {
            throw new InvalidArgumentException('Invalid timeout argument. Use Completed or Cancelled');
        }

        $this->onTimeout = $onTimeout;

        return $this;
    }

    /**
     * Initiate the registration process.
     *
     * @param null $shortCode
     * @param null $confirmationURL
     * @param null $validationURL
     * @param null $onTimeout
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function submit($shortCode = null, $confirmationURL = null, $validationURL = null, $onTimeout = null)
    {
        if ($onTimeout && $onTimeout != 'Completed' && $onTimeout = 'Cancelled') {
            throw new InvalidArgumentException('Invalid timeout argument. Use Completed or Cancelled');
        }

        $body = [
            'ShortCode'       => $shortCode ?: $this->shortCode,
            'ResponseType'    => $onTimeout ?: $this->onTimeout,
            'ConfirmationURL' => $confirmationURL ?: $this->confirmationURL,
            'ValidationURL'   => $validationURL ?: $this->validationURL
        ];

        try {
            $response = $this->makeRequest($body);

            return \json_decode($response->getBody());
        } catch (RequestException $exception) {
            $message = $exception->getResponse() ?
               $exception->getResponse()->getReasonPhrase() :
               $exception->getMessage();

            throw $this->generateException($message);
        }
    }

    /**
     * Initiate the registration request.
     *
     * @param array $body
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function makeRequest($body = [])
    {
        return $this->engine->client->request('POST', $this->endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->engine->auth->authenticate(),
                'Content-Type'  => 'application/json',
            ],
            'json' => $body,
        ]);
    }

    /**
     * @param $getReasonPhrase
     *
     * @return \Exception
     */
    private function generateException($getReasonPhrase)
    {
        return new \Exception($getReasonPhrase);
    }
}
