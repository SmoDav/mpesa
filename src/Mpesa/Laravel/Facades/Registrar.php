<?php

namespace SmoDav\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Registrar
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class Registrar extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mp_registrar';
    }
}
