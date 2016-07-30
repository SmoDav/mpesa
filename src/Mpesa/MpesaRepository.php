<?php

namespace SmoDav\Mpesa;

use SmoDav\Mpesa\Contracts\ConfigurationStore;

class MpesaRepository
{
    /**
     * The M-Pesa API Endpoint.
     *
     * @var string
     */
    public $endpoint;

    /**
     * The callback URL to be queried on transaction completion.
     *
     * @var string
     */
    public $callbackUrl;

    /**
     * The callback method to be used.
     *
     * @var string
     */
    public $callbackMethod;

    /**
     * The merchant's Paybill number.
     *
     * @var int
     */
    public $paybillNumber;

    /**
     * The transaction number generator.
     *
     * @var Transactable
     */
    public $transactionGenerator;

    /**
     * The SAG Passkey given on registration.
     *
     * @var string
     */
    public $passkey;

    /**
     * Set the system to use demo timestamp and password.
     *
     * @var bool
     */
    public $demo;

    /**
     * The configuration store that holds the configuration values.
     *
     * @var ConfigurationStore
     */
    private $store;


    /**
     * Transactor constructor.
     *
     * @param ConfigurationStore $store
     */
    public function __construct(ConfigurationStore $store)
    {
        $this->store = $store;

        $this->boot();
    }

    /**
     * Boot up the instance.
     */
    protected function boot()
    {
        $this->configure();
    }

    /**
     * Configure the instance and pick configurations from the config file.
     */
    protected function configure()
    {
        $this->setupBroker();
        $this->setupPaybill();
        $this->setNumberGenerator();
    }

    /**
     * Set up the API Broker endpoint and callback
     */
    protected function setupBroker()
    {
        $this->endpoint = $this->store->get('mpesa.endpoint');
        $this->callbackUrl = $this->store->get('mpesa.callback_url');
        $this->callbackMethod = $this->store->get('mpesa.callback_method');
    }

    /**
     * Set up Merchant Paybill account.
     */
    protected function setupPaybill()
    {
        $this->paybillNumber = $this->store->get('mpesa.paybill_number');
        $this->passkey = $this->store->get('mpesa.passkey');
        $this->demo = $this->store->get('mpesa.demo');
    }

    /**
     * Set up the transaction number generator that implements Transactable Interface.
     */
    protected function setNumberGenerator()
    {
        $this->transactionGenerator = $this->store->get('mpesa.transaction_id_handler');
    }
}
