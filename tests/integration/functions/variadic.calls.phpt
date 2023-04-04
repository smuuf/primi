<?php

use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Ex\SyntaxError;

use \Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

// Interpreter will create and use its own default config if needed.
$i = new Interpreter;

//
// First, test some valid cases - to see if Variadic args basically work.
//

$src = <<<SRC
function decorator(fn) {
	return (*args, **kwargs) => { return f"BEGIN-{fn(*args, **kwargs)}-END"; }
}

function something(a, b, *args, **kwargs) {
	return f"a = {a}, b = {b}, args = {args}, kwargs = {kwargs}"
}

decorated = decorator(something)

result = decorated(111, 222, 333, 444, kw1: 555, kw2: 666)

SRC;

Assert::noError(function() use ($i, $src) {
	$result = $i->run($src)->getScope()->getVariable('result')->getStringValue();
	Assert::same(
		'BEGIN-a = 111, b = 222, args = (333, 444), kwargs = {"kw1": 555, "kw2": 666}-END',
		$result
	);
});

//
// Now, let's test some cases that should throw specific errors.
//

//
// SyntaxError "Keyword arguments must be placed after positional arguments"
//

$src = "
function fun_is_fun(a, b, *c, **d) {}
fun_is_fun(1, **{}, *['a', 'b', 'c'])
";

assert_uncaught_error(
	fn() => $i->run($src),
	'SyntaxError',
	'#Keyword arguments must be placed after positional arguments#i',
);

// This is ok.
$src = "
function fun_is_fun(a, b, *c, **d) {}
fun_is_fun(1, *['a', 'b', 'c'], **{})
";

Assert::noError(function() use ($i, $src) {
	$i->run($src);
});

// This is also ok.
$src = "
function fun_is_fun(a, b, *c, **d) {}
fun_is_fun(1, *['a', 'b', 'c'], **{}, yay: 'yaaay')
";

Assert::noError(function() use ($i, $src) {
	$i->run($src);
});

// This is also ok.
$src = "
function fun_is_fun(a, b, *c, **d) {}
fun_is_fun(1, *['a', 'b', 'c'], *(1,2), yay: 'yaaay')
";

Assert::noError(function() use ($i, $src) {
	$i->run($src);
});

// This is NOT ok.
// (Keyword arguments must be placed after positional arguments)
$src = "
function fun_is_fun(a, b, *c, **d) {}
fun_is_fun(1, *['a', 'b', 'c'], **{}, *[])
";

assert_uncaught_error(
	fn() => $i->run($src),
	'SyntaxError',
	'#Keyword arguments must be placed after positional arguments#i',
);

// This is NOT ok.
// (Repeated keyword argument)
$src = "
f = (a, b, c) => {}
result = f(1, b: 2, b: 3, c: 4)
";

assert_uncaught_error(
	fn() => $i->run($src),
	'SyntaxError',
	"#Repeated keyword argument 'b'#i",
);
