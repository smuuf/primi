<p align="center">
  <img src="https://raw.githubusercontent.com/smuuf/primi/master/res/art/logo-sml.png" alt="Primi">
  <h1 align="center">Primi</h1>
  <p align="center">A scripting language <i><b>written in PHP</i></b> & <i><b>embeddable into PHP</b></i>.</p>
</p>

Travis CI | Code Climate
--- | ---
[![Build Status](https://travis-ci.org/smuuf/primi.svg?branch=master)](https://travis-ci.org/smuuf/primi) | [![Maintainability](https://api.codeclimate.com/v1/badges/fa9fcdf67a72b20c4af2/maintainability)](https://codeclimate.com/repos/59ed1c106d45230296000143/maintainability)

# Installation
## As a library

1. First, install Composer package: `composer require smuuf/primi`
2. Then use it like this:
```php
<?php

require __DIR__ . "/vendor/autoload.php";

$context = new \Smuuf\Primi\Context;
$interpreter = new \Smuuf\Primi\Interpreter($context);

try {
    $interpreter->run('a = 1; b = a + 2; c = "some string"; d = c + " extra thing"');
    print_r($context->getVariables());
} catch (\Smuuf\Primi\ErrorException $e) {
    die($e->getMessage());
}

```

Running this code will output:

```
Array
(
    [a] => Smuuf\Primi\Structures\NumberValue Object
        (
            [value:protected] => 1
        )

    [b] => Smuuf\Primi\Structures\NumberValue Object
        (
            [value:protected] => 3
        )

    [c] => Smuuf\Primi\Structures\StringValue Object
        (
            [value:protected] => some string
        )

    [d] => Smuuf\Primi\Structures\StringValue Object
        (
            [value:protected] => some string extra thing
        )

)

```
