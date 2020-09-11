# M-PESA API Package
[![Build Status](https://travis-ci.org/SmoDav/mpesa.svg?branch=master)](https://travis-ci.org/SmoDav/mpesa)
[![Total Downloads](https://poser.pugx.org/smodav/mpesa/d/total.svg)](https://packagist.org/packages/smodav/mpesa)
[![Latest Stable Version](https://poser.pugx.org/smodav/mpesa/v/stable.svg)](https://packagist.org/packages/smodav/mpesa)
[![Latest Unstable Version](https://poser.pugx.org/smodav/mpesa/v/unstable.svg)](https://packagist.org/packages/smodav/mpesa)
[![License](https://poser.pugx.org/smodav/mpesa/license.svg)](https://packagist.org/packages/smodav/mpesa)

This is a PHP package for the Safaricom's M-Pesa API. 
The API allows a merchant to initiate C2B online checkout (paybill via web) transactions.
The merchant submits authentication details, transaction details, callback url and callback method. 

After request submission, the merchant receives instant feedback with validity status of their requests. 
The C2B API handles customer validation and authentication via USSD push. 
The customer then confirms the transaction. If the validation of the customer fails or the customer declines the transaction, the API makes a callback to merchant. Otherwise the transaction is processed and its status is made through a callback.

If you enjoy using this package, please take a moment and [buy me some coffee.](https://rave.flutterwave.com/donate/fiqyumudlt6t)



## Installation

Pull in the package through Composer.

### Native Addon

When using vanilla PHP, modify your `composer.json` file to include:

```json
  "scripts": {
    "post-update-cmd": [
        "SmoDav\\Mpesa\\Support\\Installer::install"
    ]
  },
```

This script will copy the default configuration file to a config folder in the root directory of your project.
Now proceed to require the package.

### General Install

Run `composer require smodav/mpesa` to get the latest stable version of the package.

## Migration from previous versions

v5 of the package changes the implementation and introduces some breaking changes. Please have a look at the [CHANGELOG](https://github.com/SmoDav/mpesa/blob/master/CHANGELOG.md).

v4 of this package uses a new configuration setup. You will need to update your config file in order to upgrade v3 to v4. v2 is still incompatible since it uses the older API version.

### Laravel

When using Laravel 5.5+, the package will automatically register. For laravel 5.4 and below,
include the service provider and its alias within your `config/app.php`.

```php
'providers' => [
    SmoDav\Mpesa\Laravel\ServiceProvider::class,
],

'aliases' => [
    'STK'       => SmoDav\Mpesa\Laravel\Facades\STK::class,
    'Simulate'  => SmoDav\Mpesa\Laravel\Facades\Simulate::class,
    'Registrar' => SmoDav\Mpesa\Laravel\Facades\Registrar::class,
    'Identity'  => SmoDav\Mpesa\Laravel\Facades\Identity::class,
],
```

Publish the package specific config using:
```bash
php artisan vendor:publish
```

### Other Frameworks

To implement this package, a configuration repository is needed, thus any other framework will need to create its own implementation of the `ConfigurationStore` and `CacheStore` interfaces.

### Configuration

The package allows you to have multiple accounts. Each account will have its specific credentials and endpoints that are independent of the rest.
You will be required to set the default account to be used for all transactions, which you can override on each request you make. The package comes
with two default accounts that you can modify.

```
/*
|--------------------------------------------------------------------------
| Default Account
|--------------------------------------------------------------------------
|
| This is the default account to be used when none is specified.
*/

'default' => 'staging',

/*
|--------------------------------------------------------------------------
| File Cache Location
|--------------------------------------------------------------------------
|
| When using the Native Cache driver, this will be the relative directory
| where the cache information will be stored.
*/

'cache_location' => '../cache',

/*
|--------------------------------------------------------------------------
| Accounts
|--------------------------------------------------------------------------
|
| These are the accounts that can be used with the package. You can configure
| as many as needed. Two have been setup for you.
|
| Sandbox: Determines whether to use the sandbox, Possible values: sandbox | production
| Initiator: This is the username used to authenticate the transaction request
| LNMO:
|    shortcode: The till number
|    passkey: The passkey for the till number
|    callback: Endpoint that will be be queried on completion or failure of the transaction.
|
*/

'accounts' => [
    'staging' => [
        'sandbox' => true,
        'key' => 'your development consumer key',
        'secret' => 'your development consumer secret',
        'initiator' => 'your development username',
        'id_validation_callback' => 'http://example.com/callback?secret=some_secret_hash_key',
        'lnmo' => [
            'paybill' => 'your development paybill number',
            'shortcode' => 'your development business code',
            'passkey' => 'your development passkey',
            'callback' => 'http://example.com/callback?secret=some_secret_hash_key',
        ]
    ],

    'paybill_1' => [
        'sandbox' => false,
        'key' => 'your production consumer key',
        'secret' => 'your production consumer secret',
        'initiator' => 'your production username',
        'id_validation_callback' => 'http://example.com/callback?secret=some_secret_hash_key',
        'lnmo' => [
            'paybill' => 'your production paybill number',
            'shortcode' => 'your production business code',
            'passkey' => 'your production passkey',
            'callback' => 'http://example.com/callback?secret=some_secret_hash_key',
        ]
    ],

    'paybill_2' => [
        'sandbox' => false,
        'key' => 'your production consumer key',
        'secret' => 'your production consumer secret',
        'initiator' => 'your production username',
        'id_validation_callback' => 'http://example.com/callback?secret=some_secret_hash_key',
        'lnmo' => [
            'paybill' => 'your production paybill number',
            'shortcode' => 'your production business code',
            'passkey' => 'your production passkey',
            'callback' => 'http://example.com/callback?secret=some_secret_hash_key',
        ]
    ],
],
```

You can add as many accounts as required and switch the connection using the method `usingAccount` on `STK`, `Register` and `Simulate` as shown below.

Also, note the difference between the `business shortcode` and your `paybill number` in the configuration as getting them wrong will cost you a lot of time debugging.


## Usage

For Vanilla PHP you will need to initialize the core engine before any requests as shown below. The package comes with a vanilla php implementation of the cache and configuration store, `NativeCache` and `NativeConfig`.

The `NativeConfig` receives the custom location for the configuration file to be used as the first constructor argument. If no value is passed when creating the instance, it will use the default configuration and look for a configuration file on the root of the project under `configs` directory.

The `NativeCache` receives a custom directory path as the first constructor argument. The path denotes where the cache should store its files. If no path is provided, the default cache location in the config will be used.

```php
use GuzzleHttp\Client;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Native\NativeCache;
use SmoDav\Mpesa\Native\NativeConfig;

require "vendor/autoload.php";

$config = new NativeConfig();
$cache = new NativeCache($config->get('cache_location'));
// or
$cache = new NativeCache(__DIR__ . '/../some/awesome/directory');

$core = new Core(new Client, $config, $cache);

```

### URL Registration
#### submit(shortCode = null, confirmationURL = null, validationURL = null, onTimeout = 'Completed|Cancelled', account = null)

Register callback URLs

##### Vanilla

```php
use SmoDav\Mpesa\C2B\Registrar;

$conf = 'http://example.com/mpesa/confirm?secret=some_secret_hash_key';
$val = 'http://example.com/mpesa/validate?secret=some_secret_hash_key';


$response = (new Registrar($core))->register(600000)
    ->onConfirmation($conf)
    ->onValidation($val)
    ->submit();

/****** OR ********/
$response = (new Registrar($core))->submit(600000, $conf, $val);

```

When having multiple accounts, switch using the `usingAccount` method. We currently have `staging`, `paybill_1` and `paybill_2` with `staging` as the default:

```php
$response = (new Registrar($core))
    ->register(600000)
    ->usingAccount('paybill_1')
    ->onConfirmation($conf)
    ->onValidation($val)
    ->submit();

/****** OR ********/
$response = (new Registrar($core))->submit(600000, $conf, $val, null, 'paybill_1');
```

##### Laravel

```php
use SmoDav\Mpesa\Laravel\Facades\Registrar;

$conf = 'http://example.com/mpesa/confirm?secret=some_secret_hash_key';
$val = 'http://example.com/mpesa/validate?secret=some_secret_hash_key';

$response = Registrar::register(600000)
    ->onConfirmation($conf)
    ->onValidation($val)
    ->submit();

/****** OR ********/
$response = Registrar::submit(600000, $conf, $val);
```

Using the `paybill_1` account:

```php
use SmoDav\Mpesa\Laravel\Facades\Registrar;

$response = Registrar::register(600000)
    ->usingAccount('paybill_1')
    ->onConfirmation($conf)
    ->onValidation($val)
    ->submit();

/****** OR ********/
$response = Registrar::submit(600000, $conf, $val, null, 'paybill_1');
```

### Simulate Transaction
#### push(amount = null, number = null, reference = null, account = null, command = null)

Initiate a C2B simulation transaction request.

Note that when initiating a C2B simulation, setting the command type is optional and by default `CustomerPaybillOnline`
will be used.

##### Vanilla

```php
use SmoDav\Mpesa\C2B\Simulate;

$simulate = new Simulate($core)

$response = $simulate->request(10)
    ->from(254722000000)
    ->usingReference('Some Reference')
    ->push();

/****** OR ********/
$response = $simulate->push(10, 254722000000, 'Some Reference');
```

Using the `paybill_1` account:

```php
$response = $simulate->request(10)
    ->from(254722000000)
    ->usingReference('Some Reference')
    ->usingAccount('paybill_1')
    ->push();

/****** OR ********/
$response = $simulate->push(10, 254722000000, 'Some Reference', 'paybill_1');
```

Using the `CustomerBuyGoodsOnline` command:

```php
$response = $simulate->request(10)
    ->from(254722000000)
    ->usingReference('Some Reference')
    ->setCommand(CUSTOMER_BUYGOODS_ONLINE)
    ->push();

/****** OR ********/
$response = $simulate->push(10, 254722000000, 'Some Reference', null, CUSTOMER_BUYGOODS_ONLINE);
```


##### Laravel

```php
use SmoDav\Mpesa\Laravel\Facades\Simulate;

$response = Simulate::request(10)
    ->from(254722000000)
    ->usingReference('Some Reference')
    ->push();

/****** OR ********/
$response = Simulate::push(10, 254722000000, 'Some Reference');
```

Using the `paybill_1` account:

```php
use SmoDav\Mpesa\Laravel\Facades\Simulate;

$response = Simulate::request(10)
    ->from(254722000000)
    ->usingReference('Some Reference')
    ->usingAccount('paybill_1')
    ->push();

/****** OR ********/
$response = Simulate::push(10, 254722000000, 'Some Reference', 'paybill_1');
```

Using the `CustomerBuyGoodsOnline` command:

```php
use SmoDav\Mpesa\Laravel\Facades\Simulate;

$response = Simulate::request(10)
    ->from(254722000000)
    ->usingReference('Some Reference')
    ->setCommand(CUSTOMER_BUYGOODS_ONLINE) 
    ->push();

/****** OR ********/
$response = Simulate::push(10, 254722000000, 'Some Reference', null, CUSTOMER_BUYGOODS_ONLINE);
```

### STK PUSH
#### push(amount = null, number = null, reference = null, description = null, account = null, command = null)

Initiate a C2B STK Push request.

Note that when initiating an STK Push, setting the command type is optional and by default `CustomerPaybillOnline`
will be used.

##### Vanilla

```php
use SmoDav\Mpesa\C2B\STK;

$stk = new STK($core);

$response = $stk->request(10)
    ->from(254722000000)
    ->usingReference('Some Reference', 'Test Payment')
    ->push();

/****** OR ********/
$response = $stk->push(10, 254722000000, 'Some Reference', 'Test Payment');
```

Using the `paybill_2` account:

```php

$response = $stk->request(10)
    ->from(254722000000)
    ->usingAccount('paybill_2')
    ->usingReference('Some Reference', 'Test Payment')
    ->push();

/****** OR ********/
$response = $stk->push(10, 254722000000, 'Some Reference', 'Test Payment', 'paybill_2');
```

Using `CustomerBuyGoodsOnline` command:

```php

$response = $stk->request(10)
    ->from(254722000000)
    ->usingReference('Some Reference', 'Test Payment')
    ->setCommand(CUSTOMER_BUYGOODS_ONLINE)
    ->push();

/****** OR ********/
$response = $stk->push(10, 254722000000, 'Some Reference', 'Test Payment', null, CUSTOMER_BUYGOODS_ONLINE);
```


##### Laravel

```php
use SmoDav\Mpesa\Laravel\Facades\STK;

$response = STK::request(10)
    ->from(254722000000)
    ->usingReference('Some Reference', 'Test Payment')
    ->push();

/****** OR ********/
$response = STK::push(10, 254722000000, 'Some Reference', 'Test Payment');
```

Using the `paybill_2` account:

```php
use SmoDav\Mpesa\Laravel\Facades\STK;

$response = STK::request(10)
    ->from(254722000000)
    ->usingAccount('paybill_2')
    ->usingReference('Some Reference', 'Test Payment')
    ->push();

$response = STK::push(10, 254722000000, 'Some Reference', 'Test Payment', 'paybill_2');
```

Using the `CustomerGoodsOnline` command:

```php
use SmoDav\Mpesa\Laravel\Facades\STK;

$response = STK::request(10)
    ->from(254722000000)
    ->usingReference('Some Reference', 'Test Payment')
    ->setCommand(CUSTOMER_BUYGOODS_ONLINE) 
    ->push();

$response = STK::push(10, 254722000000, 'Some Reference', 'Test Payment', null, CUSTOMER_BUYGOODS_ONLINE);
```

### STK PUSH Transaction Validation
#### validate(merchantReferenceId, account = null)

Validate a C2B STK Push transaction.

##### Vanilla

```php
use SmoDav\Mpesa\C2B\STK;

$stk = new STK($core);
    
$response = $stk->validate('ws_CO_16022018125');
```

Using the `paybill_2` account:

```php
$response = $stk->validate('ws_CO_16022018125', 'paybill_2');
```

##### Laravel

```php
use SmoDav\Mpesa\Laravel\Facades\STK;

$response = STK::validate('ws_CO_16022018125');
```

Using the `paybill_1` account:

```php
use SmoDav\Mpesa\Laravel\Facades\STK;

$response = STK::validate('ws_CO_16022018125', 'paybill_2');
```

##### When going live, you should change the `default` value of the config file to the production account.

## License

The M-Pesa Package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
