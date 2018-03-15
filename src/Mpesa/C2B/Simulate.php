<?php

namespace SmoDav\Mpesa\C2B;

use GuzzleHttp\Exception\RequestException;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Repositories\EndpointsRepository;
use SmoDav\Mpesa\Exceptions\ErrorException;

class Simulate
{
    protected $pushEndpoint;
    protected $engine;
    protected $number;
    protected $amount;
    protected $reference;

    /**
     * Customer PayBill Online Command
     */
    const CUSTOMER_PAYBILL_ONLINE = 'CustomerPayBillOnline';

    /**
     * Customer BuyGoods Online Command
     */
    const CUSTOMER_BUYGOODS_ONLINE = 'CustomerBuyGoodsOnline';

    /**
     * Valid set of commands allowed.
     */
    const VALID_COMMANDS = [
        self::CUSTOMER_PAYBILL_ONLINE,
        self::CUSTOMER_BUYGOODS_ONLINE
    ];

    /**
     * Simulate constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine       = $engine;
        $this->pushEndpoint = EndpointsRepository::build(MPESA_SIMULATE);
    }

    /**
     * Set the request amount to be deducted.
     *
     * @param int $amount
     *
     * @return $this
     */
    public function request($amount)
    {
        if (!\is_numeric($amount)) {
            throw new \InvalidArgumentException('The amount must be numeric');
        }

        $this->amount = $amount;

        return $this;
    }

    /**
     * Set the Mobile Subscriber Number to deduct the amount from.
     * Must be in format 2547XXXXXXXX.
     *
     * @param int $number
     *
     * @return $this
     */
    public function from($number)
    {
        if (! starts_with($number, '2547')) {
            throw new \InvalidArgumentException('The subscriber number must start with 2547');
        }

        $this->number = $number;

        return $this;
    }

    /**
     * Set the product reference number to bill the account.
     *
     * @param int $reference
     *
     * @return $this
     */
    public function usingReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Set the unique command for this transaction type.
     *
     * @param string $command
     *
     * @return $this
     */
    public function setCommand($command)
    {
        if (! \in_array($command, self::VALID_COMMANDS)) {
            throw new \InvalidArgumentException('Invalid command sent');
        }

        $this->command = $command;

        return $this;
    }

    /**
     * Prepare the transaction simulation request
     *
     * @param int    $amount
     * @param int    $number
     * @param string $reference
     * @param string $command
     *
     * @throws ErrorException
     *
     * @return mixed
     */
    public function push($amount = null, $number = null, $reference = null, $command = null)
    {
        if ($this->engine->config->get('mpesa.status', 'sandbox') !== 'sandbox') {
            throw new ErrorException('Cannot simulate a transaction in the live environment.');
        }

        $shortCode = $this->engine->config->get('mpesa.short_code');

        $body = [
            'CommandID'     => $command ?: $this->command,
            'Amount'        => $amount ?: $this->amount,
            'Msisdn'        => $number ?: $this->number,
            'ShortCode'     => $shortCode,
            'BillRefNumber' => $reference ?: $this->reference,
        ];

        try {
            $response = $this->makeRequest($body);

            return \json_decode($response->getBody());
        } catch (RequestException $exception) {
            return \json_decode($exception->getResponse()->getBody());
        }
    }

    /**
     * Initiate the request.
     *
     * @param array $body
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function makeRequest($body = [])
    {
        return $this->engine->client->request('POST', $this->pushEndpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->engine->auth->authenticate(),
                'Content-Type'  => 'application/json',
            ],
            'json' => $body,
        ]);
    }
}
