<?php

namespace SmoDav\MPesa;

use Illuminate\Support\Str;
use SmoDav\MPesa\Contracts\Transactable;

/**
 * Class Generator
 *
 * @category PHP
 * @package  SmoDav\MPesa
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
        return Str::random(10);
    }
}
