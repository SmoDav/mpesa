<?php

namespace SmoDav\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Identity.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 *
 * @method static stdClass validate(string $number, callable $callback, string $account = null)
 *
 * @see \SmoDav\Mpesa\C2B\Registrar
 */
class Identity extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mp_identity';
    }
}
