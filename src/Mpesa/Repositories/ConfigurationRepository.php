<?php

namespace SmoDav\Mpesa\Repositories;

use Exception;
use SmoDav\Mpesa\Contracts\ConfigurationStore;

/**
 * Class ConfigurationRepository.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class ConfigurationRepository
{
    const SANDBOX_URL = 'https://sandbox.safaricom.co.ke/';
    const PRODUCTION_URL = 'https://api.safaricom.co.ke/';

    /**
     * @var string
     */
    private $account;

    /**
     * @var ConfigurationStore
     */
    protected $store;

    /**
     * @var array
     */
    protected $config;

    /**
     * Build up a new instance.
     *
     * @param ConfigurationStore $store
     */
    public function __construct(ConfigurationStore $store)
    {
        $this->store = $store;
        $this->config = $this->store->get('mpesa');
        $this->useAccount();
    }

    /**
     * Get the configuration instance.
     */
    public function store()
    {
        return $this->store;
    }

    /**
     * Get the configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function config($key = null, $default = null)
    {
        if (!$key) {
            return $this->config;
        }

        $key = explode('.', $key);
        $value = $this->config;

        foreach ($key as $prop) {
            if (!isset($value[$prop])) {
                return $default;
            }

            $value = $value[$prop];
        }

        return $value;
    }

    /**
     * Set the account to be used when resoving configs.
     *
     * @param string $account
     *
     * @return self
     */
    public function useAccount($account = null)
    {
        $account = $account ?: $this->config('default');

        if (!$this->config("accounts.{$account}")) {
            throw new Exception('Invalid account selected');
        }

        $this->account = $account;

        return $this;
    }

    /**
     * Get a configuration value from the store.
     *
     * @param string $key
     * @param mixed  $default
     * @param string $account
     *
     * @return mixed
     */
    public function getAccountKey($key, $default = null)
    {
        return $this->config("accounts.{$this->account}.{$key}", $default);
    }

    /**
     * Get the endpoint relative to the current
     *
     * @param string $endpoint
     * @param string $account
     *
     * @return string
     */
    public function url($endpoint)
    {
        return $this->resolveUrl(
            $this->getAccountKey('sandbox', true) ? self::SANDBOX_URL : self::PRODUCTION_URL,
            $endpoint
        );
    }

    /**
     * Resolve the provided URL
     *
     * @param string $base
     * @param string $key
     *
     * @return string
     */
    private function resolveUrl($base, $key)
    {
        return $base . trim($key, '/');
    }
}
