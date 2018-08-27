<?php

namespace SmoDav\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Registrar.
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 *
 * @method static Registrar onConfirmation(string $confirmationURL)
 * @method static Registrar onTimeout(string $onTimeout)
 * @method static Registrar onValidation(string $validationURL)
 * @method static Registrar register(string $shortCode)
 * @method static stdClass submit(string $shortCode = null, string $confirmationURL = null, string $validationURL = null, string $onTimeout = null, string $account = null)
 * @method static Registrar usingAccount(string $account)
 *
 * @see \SmoDav\Mpesa\C2B\Registrar
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
