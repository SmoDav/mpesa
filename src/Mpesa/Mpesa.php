<?php

namespace SmoDav\Mpesa;

use Illuminate\Support\Facades\Facade;

/**
 * Class Mpesa
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class Mpesa extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mpesa';
    }
}
