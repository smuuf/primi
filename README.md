<p align="center">
  <img src="https://raw.githubusercontent.com/smuuf/primi/master/res/art/logo-sml.png" alt="Primi">
  <h1 align="center">Primi</h1>
  <p align="center">A scripting language <i><b>written in PHP</i></b> & <i><b>embeddable into PHP</b></i>.</p>
</p>

<p align="center">
  Primi is meant for PHP developers who want to <b>allow their clients to write their own custom logic</b>. Primi allows you <i>(the developer)</i> to <b>execute untrusted code</b> <i>(provided simply as a string)</i> inside a sandbox, safely separated from its surroundings.
</p>

---

Travis CI | Code Climate | Packagist
--- | --- | ---
[![Build Status](https://travis-ci.org/smuuf/primi.svg?branch=master)](https://travis-ci.org/smuuf/primi) | [![Maintainability](https://api.codeclimate.com/v1/badges/13c3d5b429d281de5947/maintainability)](https://codeclimate.com/github/smuuf/primi/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/13c3d5b429d281de5947/test_coverage)](https://codeclimate.com/github/smuuf/primi/test_coverage) | [![Latest Stable Version](https://poser.pugx.org/smuuf/primi/v/stable)](https://packagist.org/packages/smuuf/primi) [![License](https://poser.pugx.org/smuuf/primi/license)](https://packagist.org/packages/smuuf/primi) [![Total Downloads](https://poser.pugx.org/smuuf/primi/downloads)](https://packagist.org/packages/smuuf/primi)

---

# Quick access
- [Language reference (syntax help)](/docs/language_reference.md)

# Rationale
Primi *- as things sometimes go in life -* began as an answer to a practical problem: I needed some general-purpose *(ie. not too much domain-specific)* scripting language that my other app's users could use to write their simple custom logic. I needed some universally usable and **primi**tive scripting thing, with familiar syntax *(`PHP-like` x `C-like` x `JS-like`)* and one that could be safely executed inside pure PHP environment *(no external depedencies on v8js, v8 and whatnot - meaning Javascript is out of the game...)*.

***Thus, Primi was (mostly as an experiment) created.***

# Installation
You can either use *Primi* as a **[standalone package](#a-standalone-installation)** `(a)` - for its development, making contributions, debugging it, or to just play with it. Or you can use *Primi* **[in your own projects](#b-as-a-library)** `(b)` by installing it as a Composer dependency.

## a) Standalone installation

1. Clone this repo.
    - `git clone https://github.com/smuuf/primi.git`
2. Install Composer dependencies.
    - `composer install`
3. Run something with Primi CLI.
    - `chmod +x ./primi && ./primi -s -c "a = 1 + 2 / 3;"`

### Convenient installation Oneliner™:
```
git clone https://github.com/smuuf/primi.git && cd primi && composer install && chmod +x ./primi && ./primi -s -c "a = 1 + 2 / 3;"
```

### Extra stuff:
- **Register Primi's CLI executable** for current user so typing `primi` will behave like a binary *(otherwise you'd need to write `./primi` and would have to be in the right directory)*:
    ```
    ./bin/registerbin
    ```

    *Note: This will add an alias in .bashrc for current user.*
- **Run tests** *(tests are located inside `./tests/` directory)*:
    ```
    ./bin/runtests
    ```
- **Run smoke tests** *(runs all Primi scripts located inside `./sample/` directory)*:
    ```
    ./bin/runsmoketests
    ```
- **Rebuild parser** *(when you modify Primi's grammar definitions, you will want to rebuild the parser to reflect the changes)*:
    ```
    ./bin/buildparser
    ```



## b) As a library

1. First, install [Primi Composer package](https://packagist.org/packages/smuuf/primi): `composer require smuuf/primi`
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

# REPL mode
Primi provides a convenient *"sandbox"* [REPL](https://en.wikipedia.org/wiki/Read%E2%80%93eval%E2%80%93print_loop) mode which can be launched via `primi -r` and later exited by entering `exit`.

![REPL example usage](https://raw.githubusercontent.com/smuuf/primi/master/res/repl-sample.gif)

In this mode, all statements are executed when entered and the result value of the last expression is returned.

# Language reference
The basics of the language syntax and data types are found here:
https://github.com/smuuf/primi/blob/master/docs/language_reference.md
