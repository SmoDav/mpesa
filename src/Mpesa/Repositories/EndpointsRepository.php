<?php

namespace SmoDav\Mpesa\Repositories;

use SmoDav\Mpesa\Contracts\ConfigurationStore;
use SmoDav\Mpesa\Exceptions\ConfigurationException;

/**
 * Class EndpointsRepository.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class EndpointsRepository
{
    /**
     * @var string
     */
    protected $packageStatus;

    /**
     * @var string
     */
    protected $baseEndpoint;

    /**
     * @var ConfigurationStore
     */
    private $store;

    /**
     * @var EndpointsRepository
     */
    public static $instance;

    /**
     * EndpointsRepository constructor.
     *
     * @param ConfigurationStore $store
     */
    public function __construct(ConfigurationStore $store)
    {
        $this->store = $store;

        $this->boot();
    }

    /**
     * Initialize the repository.
     */
    private function boot()
    {
        $this->initializeState();
        $this->setInstance();
    }

    /**
     * Validate the current package state.
     *
     * @throws ConfigurationException
     */
    private function initializeState()
    {
        $status = $this->store->get('mpesa.status', 'sandbox');

        if (!\in_array($status, ['sandbox', 'production'])) {
            throw new ConfigurationException('Invalid package status: ' . $status);
        }

        $production = $this->store->get('mpesa.production_endpoint', '');

        if (substr($production, strlen($production) - 1) !== '/') {
            $production = $production . '/';
        }

        $this->packageStatus = $status;
        $this->baseEndpoint  = $status == 'production' ?
            $production :
            MPESA_SANDBOX;
    }

    /**
     * Set the singleton instance.
     */
    private function setInstance()
    {
        self::$instance = $this;
    }

    /**
     * Get the EndpointsRepository instance.
     *
     * @return mixed
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Generate the complete endpoint.
     *
     * @param $endpoint
     *
     * @return string
     */
    public static function build($endpoint)
    {
        $instance = self::$instance;

        return $instance->baseEndpoint . $endpoint;
    }
}
