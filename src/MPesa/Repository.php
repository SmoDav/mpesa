<?php

namespace SmoDav\MPesa;

use SmoDav\MPesa\Contracts\ConfigurationStore;

class Repository
{
    /**
     * The M-Pesa API Endpoint.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * The callback URL to be queried on transaction completion.
     *
     * @var string
     */
    protected $callbackUrl;

    /**
     * The callback method to be used.
     *
     * @var string
     */
    protected $callbackMethod;

    /**
     * The merchant's Paybill number.
     *
     * @var int
     */
    protected $paybillNumber;

    /**
     * The transaction number generator.
     *
     * @var Transactable
     */
    protected $transactionGenerator;

    /**
     * The SAG Passkey given on registration.
     *
     * @var string
     */
    protected $passkey;

    /**
     * The hashed password.
     *
     * @var string
     */
    protected $password;

    /**
     * The transaction timestamp.
     *
     * @var int
     */
    protected $timestamp;

    /**
     * The transaction reference id
     *
     * @var int
     */
    protected $referenceId;

    /**
     * The amount to be deducted
     *
     * @var int
     */
    protected $amount;

    /**
     * The Mobile Subscriber number to be billed.
     * Must be in format 2547XXXXXXXX.
     *
     * @var int
     */
    protected $number;

    /**
     * The keys and data to fill in the request body.
     *
     * @var array
     */
    protected $keys;

    /**
     * The request to be sent to the endpoint
     *
     * @var string
     */
    protected $request;

    /**
     * The generated transaction number by the Transactable implementer.
     *
     * @var string
     */
    protected $transactionNumber;

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
    }

    /**
     * Set up the transaction number generator that implements Transactable Interface.
     */
    protected function setNumberGenerator()
    {
        $this->transactionGenerator = $this->store->get('mpesa.transaction_id_handler');
    }
}
