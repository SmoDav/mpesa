<?php

namespace SmoDav\Mpesa\C2B;

use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Exceptions\ErrorException;
use SmoDav\Mpesa\Repositories\ConfigurationRepository;
use SmoDav\Mpesa\Traits\MakesRequest;

/**
 * Class Simulate.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 *
 * @method Simulate from(string $number)
 * @method Simulate request(string $amount)
 * @method stdClass push(string $amount = null, string $number = null, string $reference = null, string $command = null, string $account = null)
 * @method Simulate setCommand(string $command)
 * @method Simulate usingAccount(string $account)
 * @method Simulate usingReference(string $reference)
 * @method stdClass validate(string $checkoutRequestID, string $account = null)
 */
class Simulate
{
    use MakesRequest;

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
     * Valid set of commands allowed.
     */
    const VALID_COMMANDS = [
        CUSTOMER_BUYGOODS_ONLINE,
        CUSTOMER_PAYBILL_ONLINE,
    ];

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
     */
    public function setCommand($command)
    {
        if (! in_array($command, self::VALID_COMMANDS)) {
            throw new InvalidArgumentException('Invalid command sent');
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
    public function push($amount = null, $number = null, $reference = null, $command = null, $account = null)
    {
        $account = $account ?: $this->account;
        $configs = (new ConfigurationRepository)->useAccount($account);

        if (!$configs->getAccountKey('sandbox')) {
            throw new ErrorException('Cannot simulate a transaction in the live environment.');
        }

        $shortCode = $configs->getAccountKey('lnmo.shortcode');

        $body = [
            'CommandID'     => $command ?: $this->command,
            'Amount'        => $amount ?: $this->amount,
            'Msisdn'        => $number ?: $this->number,
            'ShortCode'     => $shortCode,
            'BillRefNumber' => $reference ?: $this->reference,
        ];

        try {
            $response = $this->makeRequest(
                $body,
                Core::instance()->getEndpoint(MPESA_SIMULATE, $account),
                $account
            );

            return json_decode($response->getBody());
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
        }
    }
}
