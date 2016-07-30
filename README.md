# Flash Notifier
[![Build Status](https://travis-ci.org/SmoDav/flash.svg?branch=master)](https://travis-ci.org/SmoDav/flash)
[![Total Downloads](https://poser.pugx.org/smodav/flash/d/total.svg)](https://packagist.org/packages/smodav/flash)
[![Latest Stable Version](https://poser.pugx.org/smodav/flash/v/stable.svg)](https://packagist.org/packages/smodav/flash)
[![Latest Unstable Version](https://poser.pugx.org/smodav/flash/v/unstable.svg)](https://packagist.org/packages/smodav/flash)
[![License](https://poser.pugx.org/smodav/flash/license.svg)](https://packagist.org/packages/smodav/flash)

This is a laravel package for displaying flash notifications that extends [Sweet Alert](http://t4t5.github.io/sweetalert/) and provides an extra custom notice notification on the top left.
## Installation

Pull in the package through Composer.

Run `composer require smodav/flash`

When using Laravel 5, include the service provider and its alias within your `config/app.php`.

```php
'providers' => [
    SmoDav\Flash\FlashServiceProvider::class,
];

'aliases' => [
    'Flash' => SmoDav\Flash\Flash::class,
];
```

Publish the package specific assets and view using
```bash
php artisan vendor:publish
```
This will publish the flash view into `resources/views/vendor/smodav/flash/` directory and also its accompanying css and javascript files into their respective `resources/assets/` directory.

## Usage

The package comes with a helper function `flash()` and its respective facade `Flash`. Within your controllers or closures, use either before a redirect:

```php
public function delete()
{
    flash()->success('Users', 'Successfully banned user.');

    return redirect()->route('users.index');
}

// OR

public function delete()
{
    Flash::success('Users', 'Successfully banned user.');

    return redirect()->route('users.index');
}
```

If you would like the notification to persist till dismissed by the user, use the `persist()` method on the instance:
```php
public function delete()
{
    Flash::success('Users', 'Successfully banned user.')->persist();

    return redirect()->route('users.index');
}
```

The package has allows you to send different types of flash alerts:

- `Flash::info('Title', 'Message')`
- `Flash::success('Title', 'Message')`
- `Flash::error('Title', 'Message')`
- `Flash::warning('Title', 'Message')`

All the above can be persisted using `persist()`.

An additional `notice()` is included that provides a notice on the top right edge, however, the notice cannot be persisted:

- `Flash::notice('Message')`

```php
public function delete()
{
    Flash::notice('Successfully banned user.');

    return redirect()->route('users.index');
}
```

For a basic flash instance of type info, just use the flash helper function:
`flash(Title, Message)`

When using Laravel, this package creates flash session keys:

**Alerts**

 - `sf_title` containing the title of the flash message.
 - `sf_message` containing the actual flash message.
 - `sf_level` containing the level of flash message.
 - `sf_persist` only present when persist is used.

**Notices**

- `sf_notice_message` containing the flash notice message.

Within your views, include the `flash` view and the corresponding css and javascript files. You may modify the flash view and add more functionality to the flash instances by passing the properties described in [Sweet Alert](http://t4t5.github.io/sweetalert/) to the `sflash` instance:

```
sflash({
    title: "{{ session('sf_title') }}",
    text: "{{ session('sf_message') }}",
    type: "{{ session('sf_level') }}",
    allowOutsideClick: true,
    confirmButtonText: "Okay Man",
    showConfirmButton: true
});
```
