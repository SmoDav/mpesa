<?php

namespace SmoDav\Mpesa\C2B;

use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use SmoDav\Mpesa\Exceptions\ErrorException;
use SmoDav\Mpesa\Repositories\Endpoint;
use SmoDav\Mpesa\Traits\UsesCore;
use SmoDav\Mpesa\Traits\Validates;

class Simulate
{
    use UsesCore, Validates;

    /**
     * The simulation number
     *
     * @var string
     */
    protected $number;

    /**
     * The transaction amount
     *
     * @var string
     */
    protected $amount;

    /**
     * The transaction reference
     *
     * @var string
     */
    protected $reference;

    /**
     * The account to be used
     *
     * @var string
     */
    protected $account = null;

    /**
     * The transaction command to be used.
     *
     * @var string
     */
    protected $command = STK::CUSTOMER_PAYBILL_ONLINE;

    /**
     * Set the request amount to be deducted.
     *
     * @param int $amount
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function request($amount)
    {
        $this->validateAmount($amount);

        $this->amount = $amount;

        return $this;
    }

    /**
     * Set the Mobile Subscriber Number to deduct the amount from.
     * Must be in format 2547XXXXXXXX.
     *
     * @param int $number
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function from($number)
    {
        $this->validateNumber($number);

        $this->number = $number;

        return $this;
    }

    /**
     * Set the product reference number to bill the account.
     *
     * @param int $reference
     *
     * @return self
     */
    public function usingReference($reference)
    {
        $this->reference = $reference;

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
     * Set the unique command for this transaction type.
     *
     * @param string $command
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function setCommand($command)
    {
        if (! in_array($command, STK::VALID_COMMANDS)) {
            throw new InvalidArgumentException('Invalid command sent');
        }

        $this->command = $command;

        return $this;
    }

    /**
     * Set the properties that require validation.
     *
     * @param string|null $amount
     * @param string|null $number
     * @param string|null $command
     *
     * @return void
     */
    private function set($amount, $number, $command)
    {
        $map = [
            'amount' => 'request',
            'number' => 'from',
            'command' => 'setCommand',
        ];

        foreach ($map as $var => $method) {
            if ($$var) {
                call_user_func([$this, $method], $$var);
            }
        }
    }

    /**
     * Prepare the transaction simulation request
     *
     * @param int|null    $amount
     * @param int|null    $number
     * @param string|null $reference
     * @param string|null $account
     * @param string|null $command
     *
     * @throws ErrorException
     *
     * @return mixed
     */
    public function push($amount = null, $number = null, $reference = null, $account = null, $command = null)
    {
        $this->set($amount, $number, $command);
        $this->core->useAccount($account ?: $this->account);

        if (!$this->core->configRepository()->getAccountKey('sandbox')) {
            throw new ErrorException('Cannot simulate a transaction in the live environment.');
        }

        $shortCode = $this->core->configRepository()->getAccountKey('lnmo.shortcode');

        $body = [
            'CommandID'     => $this->command,
            'Amount'        => $this->amount,
            'Msisdn'        => $this->number,
            'ShortCode'     => $shortCode,
            'BillRefNumber' => $reference ?: $this->reference,
        ];

        try {
            $response = $this->clientRequest(
                $body,
                $this->core->configRepository()->url(Endpoint::MPESA_SIMULATE)
            );

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
        }
    }
}
