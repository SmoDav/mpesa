<?php

namespace SmoDav\Mpesa\Engine;

use GuzzleHttp\ClientInterface;
use SmoDav\Mpesa\Auth\Authenticator;
use SmoDav\Mpesa\Contracts\CacheStore;
use SmoDav\Mpesa\Contracts\ConfigurationStore;
use SmoDav\Mpesa\Native\NativeCache;
use SmoDav\Mpesa\Native\NativeConfig;
use SmoDav\Mpesa\Repositories\ConfigurationRepository;

class Core
{
    /**
     * The configuration
     *
     * @var ConfigurationRepository
     */
    private $configRepository;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Authenticator
     */
    private $auth;

    /**
     * Core constructor.
     *
     * @param ClientInterface    $client
     * @param ConfigurationStore $configStore
     * @param CacheStore         $cacheStore
     */
    public function __construct(ClientInterface $client, ConfigurationStore $configStore = null, CacheStore $cacheStore = null)
    {
        $this->client = $client;
        $this->setupStores($configStore, $cacheStore);
        $this->initialise();
    }

    /**
     * Use the native implementation of the stores.
     *
     * @return void
     */
    protected function setupStores(ConfigurationStore $configStore = null, CacheStore $cacheStore = null)
    {
        $this->configRepository = new ConfigurationRepository($configStore ?: new NativeConfig);
        $this->cache = $cacheStore ?: new NativeCache($this->configRepository->config('cache_location'));
    }

    /**
     * Initialise the Core process.
     */
    private function initialise()
    {
        $this->auth = new Authenticator($this);
    }

    /**
     * Get the configuration repository.
     *
     * @return ConfigurationRepository
     */
    public function configRepository()
    {
        return $this->configRepository;
    }

    /**
     * Get the cache store.
     *
     * @return CacheStore
     */
    public function cache()
    {
        return $this->cache;
    }

    /**
     * Get the client.
     *
     * @return ClientInterface
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Get the client.
     *
     * @return Authenticator
     */
    public function auth()
    {
        return $this->auth;
    }

    /**
     * Switch the current account
     *
     * @param string|null $account
     *
     * @return self
     */
    public function useAccount($account = null)
    {
        $this->configRepository->useAccount($account);

        return $this;
    }

    /**
     * Switch the client instance.
     *
     * @param string|null $account
     *
     * @return self
     */
    public function useClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }
}
