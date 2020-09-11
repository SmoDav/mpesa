<?php

namespace SmoDav\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class STK
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 *
 * @method static STK from(string $number)
 * @method static STK request(string $amount)
 * @method static stdClass push(string $amount = null, string $number = null, string $reference = null, string $description = null, string $account = null)
 * @method static STK usingAccount(string $account)
 * @method static STK usingReference(string $reference, string $description)
 * @method static stdClass validate(string $checkoutRequestID, string $account = null)
 *
 * @see \SmoDav\Mpesa\C2B\STK
 */
class STK extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mp_stk';
    }
}
