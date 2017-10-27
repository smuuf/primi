<p align="center">
  <img src="https://raw.githubusercontent.com/smuuf/primi/master/res/art/logo-sml.png" alt="Primi">
  <h1 align="center">Primi</h1>
  <p align="center">A scripting language <i><b>written in PHP</i></b> & <i><b>embeddable into PHP</b></i>.</p>
</p>

Travis CI | Code Climate
--- | ---
[![Build Status](https://travis-ci.org/smuuf/primi.svg?branch=master)](https://travis-ci.org/smuuf/primi) | [![Maintainability](https://api.codeclimate.com/v1/badges/fa9fcdf67a72b20c4af2/maintainability)](https://codeclimate.com/repos/59ed1c106d45230296000143/maintainability)

# Installation
You can either use *Primi* in your own projects `(a)` **or** you can use *Primi* as a standalone thing `(b)` - for developing it, debugging, or just playing with it.

## a) As a library

1. First, install Composer package: `composer require smuuf/primi`
2. Then use it like this:
```php
<?php

require __DIR__ . "/vendor/autoload.php";

$context = new \Smuuf\Primi\Context;
$interpreter = new \Smuuf\Primi\Interpreter($context);

try {

    // Let the interpreter run a source code.
    $interpreter->run('a = 1; b = a + 2; c = "some string"; d = c + " extra thing";');

    // Get defined variables from primary context and print them.
    foreach ($context->getVariables() as $name => $value) {
        printf("%s (%s) ... %s\n", $name, $value::TYPE, $value->getPhpValue());
    }
    
} catch (\Smuuf\Primi\ErrorException $e) {
    die($e->getMessage());
}

```

Running this code would output:

```
a (number) ... 1
b (number) ... 3
c (string) ... some string
d (string) ... some string extra thing

```

## b) Standalone installation

1. Clone this repo.
2. Install Composer dependencies.
3. Run something with Primi CLI.

Convenient Oneliner™:
```
git clone https://github.com/smuuf/primi.git && cd primi && composer install && chmod +x ./primi && ./primi -s -c "a = 1 + 2 / 3;"
```

Extra/optional stuff:
- ***Register Primi binary*** for current user so `primi` will behave like a binary *(otherwise you'd need to write `./primi` and would have to be in the right directory)*:
    ```
    ./bin/registerbin
    ```

    *Note: This will add an alias in .bashrc for current user*
- ***Run tests*** *(tests are located in `./tests/` directory)*:
    ```
    ./bin/runtests
    ```
