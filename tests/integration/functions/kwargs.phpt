<?php

use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Ex\SyntaxError;
use \Smuuf\Primi\Ex\ErrorException;

use \Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

// Interpreter will create and use its own default config if needed.
$i = new Interpreter;

//
// First, test some valid cases - to see if Variadic args basically work.
//

$src = <<<SRC
function f(a, b, c, d) { return f"{a}, {b}, {c}, {d}"; }
result = f(1, 2, 3, 4)
SRC;

Assert::noError(function() use ($i, $src) {
	$result = $i->run($src)->getVariable('result')->getStringValue();
	Assert::same('1, 2, 3, 4', $result);
});

$src = <<<SRC
function f(a, b, c, d) { return f"{a}, {b}, {c}, {d}"; }
result = f(1, 2, d: 3, c: 4)
SRC;

Assert::noError(function() use ($i, $src) {
	$result = $i->run($src)->getVariable('result')->getStringValue();
	Assert::same('1, 2, 4, 3', $result);
});

$src = <<<SRC
function f(a, b, c, d) { return f"{a}, {b}, {c}, {d}"; }
result = f(c: 1, d: 2, b: 3, a: 4)
SRC;

Assert::noError(function() use ($i, $src) {
	$result = $i->run($src)->getVariable('result')->getStringValue();
	Assert::same('4, 3, 1, 2', $result);
});

//
// Now, let's test some cases that should throw specific errors.
//

//
// Error "Repeated keyword argument"
//

$src = <<<SRC
function f(a, b, c, d) { return f"{a}, {b}, {c}, {d}"; }
result = f(a: 1, b: 2, b: 3, c: 4, d: 5)
SRC;

Assert::exception(function() use ($i, $src) {
	$i->run($src);
}, SyntaxError::class, "#Repeated keyword argument 'b'#");

//
// SyntaxError "Keyword arguments must be placed after positional arg"
// Even though there are more missing arguments, 'b' is found first.
//

$src = <<<SRC
function f(a, b, c, d) { return f"{a}, {b}, {c}, {d}"; }
result = f(1, d: 4, 2)
SRC;

Assert::exception(function() use ($i, $src) {
	$i->run($src);
}, SyntaxError::class, "#Keyword arguments must be placed after positional arg.*#");

//
// Error "Missing required argument" #1
// Even though there are more missing arguments, 'b' is found first.
//

$src = <<<SRC
function f(a, b, c, d) { return f"{a}, {b}, {c}, {d}"; }
result = f(a: 1)
SRC;

Assert::exception(function() use ($i, $src) {
	$i->run($src);
}, ErrorException::class, "#Missing required argument 'b'#");

//
// Error "Missing required argument" #2
//

$src = <<<SRC
function f(a, b, c, d) { return f"{a}, {b}, {c}, {d}"; }
result = f(a: 1, d: 4, b: 2)
SRC;

Assert::exception(function() use ($i, $src) {
	$i->run($src);
}, ErrorException::class, "#Missing required argument 'c'#");

//
// Error "Unexpected keyword argument"
//

$src = <<<SRC
function f(a, b, c, d) { return f"{a}, {b}, {c}, {d}"; }
result = f(1, 2, 3, 4, xxx: 123, yyy: 456)
SRC;

Assert::exception(function() use ($i, $src) {
	$i->run($src);
}, ErrorException::class, "#Unexpected keyword argument 'xxx'#");

//
// Error "Argument ... passed multiple times"
//

$src = <<<SRC
function f(a, b, c, d) { return f"{a}, {b}, {c}, {d}"; }
result = f(1, 2, 3, 4, a: 123, b: 456)
SRC;

Assert::exception(function() use ($i, $src) {
	$i->run($src);
}, ErrorException::class, "#Argument 'a' passed multiple times#");
