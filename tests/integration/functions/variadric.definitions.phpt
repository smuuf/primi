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
function f(a, b, *c, **d) { return f"{a}, {b}, args: {c}, kwargs: {d}"; }
result = f(1, 2, 3, 4)
SRC;

Assert::noError(function() use ($i, $src) {
	$result = $i->run($src)->getVariable('result')->getStringValue();
	Assert::same('1, 2, args: (3, 4), kwargs: {}', $result);
});

$src = <<<SRC
function f(a, b, *c, **d) { return f"{a}, {b}, args: {c}, kwargs: {d}"; }
result = f(1, 2, 3, 4, kw1: 'hi', kw2: 'hello')
SRC;

Assert::noError(function() use ($i, $src) {
	$result = $i->run($src)->getVariable('result')->getStringValue();
	Assert::same('1, 2, args: (3, 4), kwargs: {"kw1": "hi", "kw2": "hello"}', $result);
});

$src = <<<SRC
function f(a, *b, c) { return f"a:{a}, b:{b}, c:{c}"; }
result = f(1, 2, 3, 4, c:5)
SRC;

Assert::noError(function() use ($i, $src) {
	$result = $i->run($src)->getVariable('result')->getStringValue();
	Assert::same('a:1, b:(2, 3, 4), c:5', $result);
});

$src = <<<SRC
function f(a, *b, c) { return f"a:{a}, b:{b}, c:{c}"; }
result = f(1, 2, 3, 4)
SRC;

Assert::exception(function() use ($i, $src) {
	$i->run($src);
}, ErrorException::class, "#missing required argument 'c'#i");

//
// Now, let's test some cases that should throw specific errors.
//

//
// SyntaxError "Syntax error"
// Variadic "**kwargs" must be placed after variadic "*args".
//

$src = "function f(a, **b, *c) { }";

Assert::exception(function() use ($i, $src) {
	$i->run($src);
}, SyntaxError::class);
