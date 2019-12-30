# Introduction

This package is an object-oriented wrapper around BC Math PHP extension.
It allows you to use OOP when working when decimal numbers.
This package also adds support for exponent numbers.

## Installation

```shell script
composer require antonvlasenko/decimal
```

## Running tests

```shell script
composer run tests
```

Note: there are other useful commands available. Check `composer.json` file.

## Example

```php
use AntonVlasenko\Decimal\Decimal;

Decimal::$SCALE = 20; // Sets precision

$decimal = new Decimal('5E2');

echo $decimal->divideBy(3);
// 166.66666666666666666666
```