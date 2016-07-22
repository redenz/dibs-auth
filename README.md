# Parse Auth

Requires LaraParse

Extends LaraParse to check user roles on login


## Installation

First, include parse-auth in your `composer.json`:

```bash
composer require dibs/parse-auth
```

Then load the service provider in your `config/app.php`:

```php
'Dibs\DibsServiceProvider'
```

You'll also need to publish the config, so you can provide your keys:

```bash
php artisan vendor:publish  --provider="Dibs\DibsServiceProvider" --tag="config"
```


### Auth Provider

ParseAuth provides a driver for Laravel's built-in auth system to work with Parse. To use it, simply go to your `config/auth.php` and update the `'driver'` key to `'dibs'`

You may then use `Auth::attempt()` and friends as normal.

