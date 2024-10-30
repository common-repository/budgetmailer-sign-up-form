# PHP Client for BudgetMailer API

This Repository contains PHP BudgetMailer API Client. To use this client, you must have a BudgetMailer Account.

## Requirements

- PHP >= 5.3
- [PHP module HASH](http://php.net/manual/en/book.hash.php)
- [PHP module JSON](http://php.net/manual/en/book.json.php)
- [PHP module Sockets](http://php.net/manual/en/book.sockets.php)
- [BudgetMailer API Account](https://www.budgetmailer.nl/aanmelden.php)

## Installation

### Composer

Client can be easily installed using [Composer](https://getcomposer.org/). The Package name is: `professio/budgetmailer-php-api`.

Installation using composer: `composer require "professio/budgetmailer-php-api:1.0.*"`

Example of `composer.json`:

```
{
    "require": {
        "professio/budgetmailer-php-api": "1.0.*"
    }
}
```

### Single file distribution

You can find single file distribution with concatenated classes in `build/dist/budgetmailer-php-api.php` file, 
which you can include to your project easily without using composer or autoloading.

### Caching

If you want to use file cache, please make sure cache directory is writable by user of the code / web server.
Also don't forget cache directory MUST NOT be accessible by web users.

## Configuration

Configuration is associative array of configuration directives as follows:

```
$config = array(
    // enable or disable cache
    'cache' => true,
    // cache directory (must be writeable, must end with /)
    'cacheDir' => '/tmp/',
    // api endpoint, please do not change this value, unless instructed to
    'endPoint' => 'https://api.budgetmailer.com/',
    // your API key
    'key' => 'INSERTAPIKEY',
    // name of the budgetmailer list you want to use as a default
    'list' => 'INSERTLISTNAME',
    // your API secret
    'secret' => 'INSERTAPISECRET',
    // advanced: socket timeout
    'timeOutSocket' => 10,
    // advanced: socket stream read timeout
    'timeOutStream' => 10,
    // cache time to live in seconds (3600 sec = 1 hour)
    'ttl' => 3600,
);
```

## Running

Install the client by Composer or include/require it as single file distribution version. 
Use the configuration example, and set following required keys:

- `key`: API key
- `secret`: API secret
- `list`: Default list ID or Name

Then pass the configuration to the client:

```
<?php

use BudgetMailer\Api\Client;

try {
  $config = array(/* See configuration example */);
  $client = Client::getInstance($config);

  print $client->isConnected() ? 'Huray!' : 'Yay...';
} catch (\Throwable $e) {
  print 'Something went wrong: ' . $e->getMessage();
}

```

### Examples

You can find more examples covering most of the use cases in: `build/examples/example.php`.

## Files Overview

- `build/`: Build Files
- `build/dist/`: Single File Distribution
- `build/docs/`: PHPDoc related Files
- `build/examples/`: Additional Examples
- `build/tests/`: PHPUnit related Files
- `src/`: all PHP Classes
- `src/BudgetMailer/Api/`: all PHP API Client Classes
- `src/BudgetMailer/Test/`: all PHPUnit Test Classes
- `.gitignore`: Gitignore File
- `LICENSE`: Full Text of MIT License
- `README.md`: This File
- `composer.json`: Composer Package Definition

## Copyright

MIT License

## Contact Information

- Email: [info@budgetmailer.nl](mailto:info@budgetmailer.nl)
- Website: [BudgetMailer](https://www.budgetmailer.nl/index.php)

## Changelog

- `1.0.3` (2017-01-19):
    - Added missing test for bulk delete contacts
    - Fixed cache path in config example + changed comment
    - Fixed paths in `*.xml.dist files`
- `1.0.2` (2016-12-13):
    - Added `build/docs/phpdoc.xml.dist` for phpdocumentor
    - Added http headers reading methods to `\BudgetMailer\Api\Client\Http`
    - Added new bulk methods: `\BudgetMailer\Api\Client::deleteContacts()`, `\BudgetMailer\Api\Client::postContactsBulk()`, `\BudgetMailer\Api\Client::postContactsUnsubscribe()`, 
    - Added some missing methods and properties comments
    - Changed files headers to reflect license change
    - Changed license from GPL2 to MIT
    - Changed tests to reflect the code changes
    - Client's `\BudgetMailer\Api\Client::deleteTag()` now handling missing tag
    - Composer.json patched (missing PHP modules requirements and wrong license)
    - Improved `build/*.php` build scripts (build docs, single file distribution, do tests)
    - Moved all distribution, documentation, examples and tests to `build/` directory
    - Quality assurance changes (various code quality improvements)
- `1.0.1` (2016-04-08):
    - Added `\BudgetMailer\Api\Client::$defaultConfig` and `\BudgetMailer\Api\Client::getInstance()` to make usage easier
    - Added `dist/config.php` to `.gitignore` and added `dist/config.php.dist` file
    - Added missing trailing slash to `cacheDir` configuration directive
    - Added tests to reflect the changes
    - Fixed `caching` issue (was always disabled)
    - Improved build script `dist/build.php`
    - Renamed composer package from `professio/php-budgetmailer` to `professio/budgetmailer-php-api`
- `1.0.0` (2015-09-24):
    - Initial Version
