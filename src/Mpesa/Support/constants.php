<?php

const MPESA_AUTH     = 'oauth/v1/generate?grant_type=client_credentials';
const MPESA_ID_CHECK = 'mpesa/checkidentity/v1/query';

const MPESA_REGISTER = 'mpesa/c2b/v1/registerurl';
const MPESA_SIMULATE = 'mpesa/c2b/v1/simulate';
const MPESA_STK_PUSH = 'mpesa/stkpush/v1/processrequest';
const MPESA_STK_PUSH_VALIDATE = 'mpesa/stkpushquery/v1/query';

const MPESA_SANDBOX = 'https://sandbox.safaricom.co.ke/';

const CUSTOMER_PAYBILL_ONLINE = 'CustomerPayBillOnline';
const CUSTOMER_BUYGOODS_ONLINE = 'CustomerBuyGoodsOnline';
