<?php

namespace SmoDav\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Simulate.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 *
 * @method static Simulate from(string $number)
 * @method static Simulate request(string $amount)
 * @method static stdClass push(string $amount = null, string $number = null, string $reference = null, string $command = null, string $account = null)
 * @method static Simulate setCommand(string $command)
 * @method static Simulate usingAccount(string $account)
 * @method static Simulate usingReference(string $reference)
 * @method static stdClass validate(string $checkoutRequestID, string $account = null)
 *
 * @see \SmoDav\Mpesa\C2B\Simulate
 */
class Simulate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mp_simulate';
    }
}
