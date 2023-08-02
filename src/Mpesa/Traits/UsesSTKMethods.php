<?php

namespace SmoDav\Mpesa\Traits;

use InvalidArgumentException;
use SmoDav\Mpesa\C2B\STK;

trait UsesSTKMethods
{
    /**
     * The mobile number.
     *
     * @var string
     */
    protected $number;

    /**
     * The amount to request.
     *
     * @var int
     */
    protected $amount;

    /**
     * The transaction reference.
     *
     * @var string
     */
    protected $reference;

    /**
     * The transaction description.
     *
     * @var string
     */
    protected $description;

    /**
     * The MPesa account to be used.
     *
     * @var string
     */
    protected $account = null;

    /**
     * The transaction command to be used.
     *
     * @var string
     */
    protected $command = self::CUSTOMER_PAYBILL_ONLINE;

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
     * Set the product reference number to bill the account.
     *
     * @param int    $reference
     * @param string $description
     *
     * @return self
     */
    public function usingReference($reference, $description)
    {
        $this->reference = $reference;
        $this->description = $description;

        return $this;
    }

    /**
     * Set the request amount to be deducted.
     *
     * @param int $amount
     *
     * @throws InvalidArgumentException
     *
     * @return self
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
     * @throws InvalidArgumentException
     *
     * @return self
     */
    public function from($number)
    {
        $this->validateNumber($number);

        $this->number = $number;

        return $this;
    }

    /**
     * Set the unique command for this transaction type.
     *
     * @param string $command
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    public function setCommand($command)
    {
        if (!in_array($command, STK::VALID_COMMANDS)) {
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
        if ($amount) {
            $this->request($amount);
        }
        if ($number) {
            $this->from($number);
        }
        if ($command) {
            $this->setCommand($command);
        }
    }
}
