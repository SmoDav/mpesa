<?php

namespace SmoDav\Mpesa\Auth;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Exceptions\ConfigurationException;
use SmoDav\Mpesa\Exceptions\ErrorException;
use SmoDav\Mpesa\Repositories\Endpoint;

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
    const AC_TOKEN = 'MP:';

    /**
     * @var Core
     */
    private $core;

    public function __construct(Core $core)
    {
        $this->core = $core;
    }

    /**
     * Remove all the access tokens
     *
     * @return void
     */
    public function flushTokens()
    {
        collect($this->core->configRepository()->config('accounts'))
            ->each(function ($account) {
                $this->core->cache()->pull($this->getCacheKey($account['key'], $account['secret']));
            });
    }

    /**
     * Get the cache key for the given key and secret
     *
     * @param string $key
     * @param string $secret
     *
     * @return void
     */
    protected function getCacheKey($key, $secret)
    {
        return self::AC_TOKEN . "{$key}{$secret}";
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
        $key = $this->core->configRepository()->getAccountKey('key');
        $secret = $this->core->configRepository()->getAccountKey('secret');
        $cacheKey = $this->getCacheKey($key, $secret);

        if ($token = $this->core->cache()->get($cacheKey)) {
            return $token;
        }

        try {
            $response = $this->makeRequest($key, $secret);
            $body = json_decode($response->getBody());
            $this->saveCredentials($cacheKey, $body);

            return $body->access_token;
        } catch (RequestException $exception) {
            $message = $exception->getResponse() ?
               $exception->getResponse()->getReasonPhrase() :
               $exception->getMessage();

            throw $this->generateException($message);
        }
    }

    /**
     * Initiate the authentication request.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function makeRequest($key, $secret)
    {
        $credentials = base64_encode($key . ':' . $secret);
        $endpoint = $this->core->configRepository()->url(Endpoint::MPESA_AUTH);

        return $this->core->client()->request('GET', $endpoint, [
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
    private function saveCredentials($key, $credentials)
    {
        $ttl = Carbon::now()->addSeconds($credentials->expires_in)->subMinute();

        $this->core->cache()->put($key, $credentials->access_token, $ttl);
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
        switch (strtolower($reason)) {
            case 'bad request: invalid credentials':
                return new ConfigurationException('Invalid consumer key and secret combination');
            default:
                return new ErrorException($reason);
        }
    }
}
