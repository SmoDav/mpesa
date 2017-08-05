<?php

use SmoDav\Mpesa\Repositories\EndpointsRepository;

function mpesa_endpoint($endpoint)
{
    return EndpointsRepository::build($endpoint);
}
