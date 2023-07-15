<?php

namespace SmoDav\Mpesa\Repositories;

class Endpoint
{
    public const MPESA_AUTH = 'oauth/:version/generate?grant_type=client_credentials';

    public const MPESA_ID_CHECK = 'mpesa/checkidentity/:version/processrequest';

    public const MPESA_REGISTER = 'mpesa/c2b/:version/registerurl';

    public const MPESA_SIMULATE = 'mpesa/c2b/:version/simulate';

    public const MPESA_LNMO = 'mpesa/stkpush/:version/processrequest';

    public const MPESA_LNMO_VALIDATE = 'mpesa/stkpushquery/:version/query';

    public const CUSTOMER_PAYBILL_ONLINE = 'CustomerPayBillOnline';

    public const CUSTOMER_BUYGOODS_ONLINE = 'CustomerBuyGoodsOnline';

    public static function getVersion()
    {
        return config('mpesa.version', 'v1');
    }
    
    public static function getEndpoint($endpoint, $version)
    {
        return str_replace(':version', $version, $endpoint);
    }
}
