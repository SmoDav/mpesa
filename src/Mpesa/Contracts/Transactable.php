<?php

namespace SmoDav\Mpesa\Contracts;

/**
 * Interface Transactable
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
interface Transactable
{
    /**
     * Generate transaction number for the request
     *
     * @return mixed
     */
    public static function generateTransactionNumber();
}
