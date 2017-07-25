<?php
/*
 *   This file is part of the Smodav Mpesa library.
 *
 *   Copyright (c) 2016 SmoDav
 *
 *   For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 */
namespace SmoDav\Mpesa;

use SmoDav\Mpesa\Contracts\Transactable;

/**
 * Class Generator
 *
 * @category PHP
 *
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
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = \strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < 17; $i++) {
            $randomString .= $characters[\rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
