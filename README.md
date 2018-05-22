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

v3 of this API uses the new M-Pesa API thus it might break some parts of the code in the previous versions.
Checkout the 2.0 Branch for the older version.

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

This will publish the M-Pesa configuration file into the `config` directory as
`mpesa.php`. This file contains all the configurations required to use the package. 
When going live edit the config and set the `production_endpoint` e.g.

```php
'production_endpoint' => 'https://production.safaricom.co.ke/'
```

### Other Frameworks

To implement this package, a configuration repository is needed, thus any other
framework will need to create its own implementation of the `ConfigurationStore` and `CacheStore`
interface.

## Usage

For Vanilla PHP you will need to initialize the core engine before any requests below.
```php
use GuzzleHttp\Client;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Native\NativeCache;
use SmoDav\Mpesa\Native\NativeConfig;


require "vendor/autoload.php";


$config = new NativeConfig();
$cache = new NativeCache($config);
$engine = new Core(new Client, $config, $cache);

```


### URL Registration
#### submit(shortCode = null, confirmationURL = null, validationURL = null, onTimeout = 'Completed|Cancelled')

Register callback URLs

##### Vanilla

```php
use SmoDav\Mpesa\C2B\Registrar;

$registrar = new Registrar($engine);
    
// fluent implementation
$response = $registrar->register(600000)
        ->onConfirmation('https://payments.smodavproductions.com/checkout.php')
        ->onValidation('https://payments.smodavproductions.com/checkout.php')
        ->submit();
        
// one function
$response = $registrar->submit(600000, 'https://payments.smodavproductions.com/checkout.php', 'https://payments.smodavproductions.com/checkout.php');
```

##### Laravel

```php
// fluent implementation
$response = \Registrar::register(600000)
        ->onConfirmation('https://payments.smodavproductions.com/checkout.php')
        ->onValidation('https://payments.smodavproductions.com/checkout.php')
        ->submit();
        
// one function
$response = \Registrar::submit(600000, 'https://payments.smodavproductions.com/checkout.php', 'https://payments.smodavproductions.com/checkout.php');
```


### Simulate Transaction
#### push(amount = null, number = null, reference = null, command = null)

Initiate an C2B simulation transaction request.

##### Vanilla

```php
use SmoDav\Mpesa\C2B\Simulate;

$simulate = new Simulate($engine);
    
// fluent implementation
$response = $simulate->request(10)
    ->from(254722000000)
    ->usingReference('f4u239fweu')
    ->setCommand(CUSTOMER_PAYBILL_ONLINE)
    ->push();
        
// one function
$response = $simulate->push(10, 254722000000, 'f4u239fweu', CUSTOMER_PAYBILL_ONLINE);
```

##### Laravel

```php
// fluent implementation
$response = \Simulate::request(10)
    ->from(254722000000)
    ->usingReference('f4u239fweu')
    ->setCommand(CUSTOMER_PAYBILL_ONLINE)
    ->push();
        
// one function
$response = \Simulate::push(10, 254722000000, 'f4u239fweu', CUSTOMER_PAYBILL_ONLINE);

```


### STK PUSH
#### push(amount = null, number = null, reference = null, description = null)

Initiate an C2B STK Push request.

##### Vanilla

```php
use SmoDav\Mpesa\C2B\STK;

$stk = new STK($engine);
    
// fluent implementation
$response = $stk->request(10)
    ->from(254722000000)
    ->usingReference('f4u239fweu', 'Test Payment')
    ->push();
        
// one function
$response = $stk->push(10, 254722000000, 'f4u239fweu', 'Test Payment');
```

##### Laravel

```php
// fluent implementation
$response = \STK::request(10)
    ->from(254722000000)
    ->usingReference('f4u239fweu', 'Test Payment')
    ->push();
        
// one function
$response = \STK::push(10, 254722000000, 'f4u239fweu', 'Test Payment');

```


### STK PUSH Transaction Validation
#### validate(merchantReferenceId)

Validate a C2B STK Push transaction.

##### Vanilla

```php
use SmoDav\Mpesa\C2B\STK;

$stk = new STK($engine);
    
$response = $stk->validate('ws_CO_16022018125');
```

##### Laravel

```php
$response = \STK::validate('ws_CO_16022018125');

```


### Identity
#### validate(number)

Validate the phone number and get details about it.

##### Vanilla

```php
use SmoDav\Mpesa\C2B\Identity;

$identity = new Identity($engine);
$response = $identity->validate(254722000000);
```

##### Laravel

```php
$response = \Identity::validate(254722000000);
```

## License

The M-Pesa Package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

