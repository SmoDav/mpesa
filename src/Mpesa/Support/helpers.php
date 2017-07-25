<?php
/*
 *   This file is part of the Smodav Mpesa library.
 *
 *   Copyright (c) 2016 SmoDav
 *
 *   For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 */
function mpesa($amount = null, $subscriberNumber = null, $referenceId = null)
{
    $cashier = app('mpesa');

    if (\func_num_args() == 0) {
        return $cashier;
    }

    if (\func_num_args() == 1) {
        return $cashier->request($amount);
    }

    if (\func_num_args() == 2) {
        return $cashier->request($amount)->from($subscriberNumber);
    }

    return $cashier->request($amount)->from($subscriberNumber)->usingReferenceId($referenceId);
}
