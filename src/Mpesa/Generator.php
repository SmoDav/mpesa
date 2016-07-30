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
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 17; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
