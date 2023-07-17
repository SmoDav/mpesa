<?php

namespace SmoDav\Mpesa\Repositories;

class Endpoint
{
    public const MPESA_AUTH = 'oauth/v1/generate?grant_type=client_credentials';

    public const MPESA_ID_CHECK = 'mpesa/checkidentity/v1/processrequest';

    public const MPESA_REGISTER = 'mpesa/c2b/v1/registerurl';

    public const MPESA_SIMULATE = 'mpesa/c2b/v1/simulate';

    public const MPESA_LNMO = 'mpesa/stkpush/v1/processrequest';

    public const MPESA_LNMO_VALIDATE = 'mpesa/stkpushquery/v1/query';

    public const CUSTOMER_PAYBILL_ONLINE = 'CustomerPayBillOnline';

    public const CUSTOMER_BUYGOODS_ONLINE = 'CustomerBuyGoodsOnline';
}
