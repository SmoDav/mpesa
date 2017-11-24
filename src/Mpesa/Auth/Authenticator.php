<?php

namespace SmoDav\Mpesa\Auth;

use GuzzleHttp\Exception\RequestException;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Exceptions\ErrorException;
use SmoDav\Mpesa\Exceptions\ConfigurationException;
use SmoDav\Mpesa\Repositories\EndpointsRepository;

/**
 * Class Authenticator.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class Authenticator
{
    /**
     * Cache key.
     */
    const AC_TOKEN = 'MP_AC_T';

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var Core
     */
    protected $engine;

    /**
     * @var Authenticator
     */
    protected static $instance;

    /**
     * Authenticator constructor.
     *
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        $this->engine   = $core;
        $this->endpoint = EndpointsRepository::build(MPESA_AUTH);
        self::$instance = $this;
    }

    /**
     * Get the access token required to transact.
     *
     * @return mixed
     *
     * @throws ConfigurationException
     */
    public function authenticate()
    {
        if ($token = $this->engine->cache->get(self::AC_TOKEN)) {
            return $token;
        }

        try {
            $response = $this->makeRequest();
            $body     = \json_decode($response->getBody());
            $this->saveCredentials($body);

            return $body->access_token;
        } catch (RequestException $exception) {
            $message = $exception->getResponse() ?
               $exception->getResponse()->getReasonPhrase() :
               $exception->getMessage();
            
            throw $this->generateException($message);
        }
    }

    /**
     * Throw a contextual exception.
     *
     * @param $reason
     *
     * @return ErrorException|ConfigurationException
     */
    private function generateException($reason)
    {
        switch (\strtolower($reason)) {
            case 'bad request: invalid credentials':
                return new ConfigurationException('Invalid consumer key and secret combination');
            default:
                return new ErrorException($reason);
        }
    }

    /**
     * Generate the base64 encoded authorization key.
     *
     * @return string
     */
    private function generateCredentials()
    {
        $key    = $this->engine->config->get('mpesa.consumer_key');
        $secret = $this->engine->config->get('mpesa.consumer_secret');

        return \base64_encode($key . ':' . $secret);
    }

    /**
     * Initiate the authentication request.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function makeRequest()
    {
        $credentials = $this->generateCredentials();

        return $this->engine->client->request('GET', $this->endpoint, [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    /**
     * Store the credentials in the cache.
     *
     * @param $credentials
     */
    private function saveCredentials($credentials)
    {
        $ttl = ($credentials->expires_in / 60) - 2;

        $this->engine->cache->put(self::AC_TOKEN, $credentials->access_token, $ttl);
    }
}
