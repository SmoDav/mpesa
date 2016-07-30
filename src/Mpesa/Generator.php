<?php

namespace SmoDav\Mpesa;

use Illuminate\Support\Str;
use SmoDav\Mpesa\Contracts\Transactable;

/**
 * Class Generator
 *
 * @category PHP
 * @package  SmoDav\Mpesa
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class Generator implements Transactable
{
    /**
     * Generate a random transaction number
     *
     * @return string
     */
    public static function generateTransactionNumber()
    {
        return Str::random(16);
    }
}
