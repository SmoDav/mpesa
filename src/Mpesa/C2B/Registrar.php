<?php

namespace SmoDav\Mpesa\C2B;

use Exception;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use SmoDav\Mpesa\Repositories\Endpoint;
use SmoDav\Mpesa\Traits\UsesCore;

class Registrar
{
    use UsesCore;

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
     * The account to be used
     *
     * @var string
     */
    protected $account = null;

    /**
     * Submit the short code to be registered.
     *
     * @param $shortCode
     *
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
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
     * Initiate the registration process.
     *
     * @param string|null $shortCode
     * @param string|null $confirmationURL
     * @param string|null $validationURL
     * @param string|null $onTimeout
     * @param string|null $account
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function submit($shortCode = null, $confirmationURL = null, $validationURL = null, $onTimeout = null, $account = null)
    {
        if ($onTimeout) {
            $this->onTimeout($onTimeout);
        }

        $this->core->useAccount($account ?: $this->account);

        $body = [
            'ShortCode'       => $shortCode ?: $this->shortCode,
            'ResponseType'    => $this->onTimeout,
            'ConfirmationURL' => $confirmationURL ?: $this->confirmationURL,
            'ValidationURL'   => $validationURL ?: $this->validationURL
        ];

        try {
            $response = $this->clientRequest(
                $body,
                $this->core->configRepository()->url(Endpoint::MPESA_REGISTER)
            );

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            $message = $exception->getResponse() ?
               $exception->getResponse()->getReasonPhrase() :
               $exception->getMessage();

            throw new Exception($message);
        }
    }
}
