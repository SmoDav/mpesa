<?php

function mpesa($amount = null, $subscriberNumber = null, $referenceId = null)
{
    $cashier = app('mpesa');

    if (func_num_args() == 0) {
        return $cashier;
    }

    if (func_num_args() == 1) {
        return $cashier->request($amount);
    }

    if (func_num_args() == 2) {
        return $cashier->request($amount)->from($subscriberNumber);
    }

    return $cashier->request($amount)->from($subscriberNumber)->usingReferenceId($referenceId);
}
