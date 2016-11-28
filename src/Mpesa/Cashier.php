<?php
namespace SmoDav\Mpesa;

/**
 * Class Cashier
 *
 * @category PHP
 * @package  SmoDav\Mpesa
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class Cashier
{
    /**
     * The amount to be deducted.
     *
     * @var int
     */
    protected $amount;

    /**
     * The Mobile Subscriber Number.
     *
     * @var int
     */
    protected $number;

    /**
     * The product reference identifier.
     *
     * @var int
     */
    protected $referenceId;
    /**
     * The transaction handler.
     *
     * @var Transactor
     */
    private $transactor;

    /**
     * Cashier constructor.
     *
     * @param Transactor $transactor
     */
    public function __construct(Transactor $transactor)
    {
        $this->transactor = $transactor;
    }

    /**
     * Override the config pay bill number and pass key.
     *
     * @param $payBillNumber
     * @param $payBillPassKey
     *
     * @return $this
     */
    public function setPayBill($payBillNumber, $payBillPassKey)
    {
        $this->transactor->setPayBill($payBillNumber, $payBillPassKey);

        return $this;
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
        if (!is_numeric($amount)) {
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
     * @param int $referenceId
     *
     * @return $this
     */
    public function usingReferenceId($referenceId)
    {
        if (!is_numeric($referenceId)) {
            throw new \InvalidArgumentException('The reference id must be numeric');
        }

        $this->referenceId = $referenceId;

        return $this;
    }

    /**
     * Initiate the transaction
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function transact()
    {
        return $this->transactor->process($this->amount, $this->number, $this->referenceId);
    }
}
