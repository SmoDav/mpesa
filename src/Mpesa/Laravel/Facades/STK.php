<?php

namespace SmoDav\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class STK
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
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
