<?php

namespace SmoDav\Mpesa\Traits;

use InvalidArgumentException;

trait Validates
{
    /**
     * Check if the provided number is valid.
     *
     * @param string $number
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    protected function validateNumber($number)
    {
    }

    /**
     * Check if the amount is numeric.
     *
     * @param string|int|float $amount
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    protected function validateAmount($amount)
    {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException('The amount must be numeric');
        }
    }
}
