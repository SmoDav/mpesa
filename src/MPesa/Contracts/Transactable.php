<?php
namespace SmoDav\MPesa\Contracts;

/**
 * Interface Transactable
 *
 * @category PHP
 * @package  SmoDav\MPesa\Contracts
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
