<?php

namespace SmoDav\Mpesa\Traits;

use Illuminate\Support\Str;
use InvalidArgumentException;

trait Validates
{
    /**
     * Check if the provided number is valid.
     *
     * @param string $number
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function validateNumber($number)
    {
        if (!Str::startsWith($number, '2547')) {
            throw new InvalidArgumentException('The subscriber number must begin with 2547');
        }
    }

    /**
     * Check if the amount is numeric.
     *
     * @param string|int|float $amount
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function validateAmount($amount)
    {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException('The amount must be numeric');
        }
    }
}
