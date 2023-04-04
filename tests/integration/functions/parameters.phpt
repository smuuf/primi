<?php

use \Smuuf\Primi\Ex\SyntaxError;
use \Smuuf\Primi\Interpreter;

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

//
// Now, let's test some cases that should throw specific errors.
//

//
// SyntaxError "Duplicate parameter '$paramName' in function" #1
//

$src = "function f(a, X, X, d) { }";

assert_uncaught_error(
	fn() => $i->run($src),
	'SyntaxError',
	"#Duplicate parameter name 'X'#i",
);

//
// SyntaxError "Duplicate parameter '$paramName' in function" #2
//

$src = "function f(a, X, *X, d) { }";

assert_uncaught_error(
	fn() => $i->run($src),
	'SyntaxError',
	"#Duplicate parameter name 'X'#i",
);

//
// Error "Duplicate parameter name '$paramName'" #3
// Even though there are more duplicated parameters, X is found first.
//

$src = "function f(a, X, c, X, Y, Y) { }";

assert_uncaught_error(
	fn() => $i->run($src),
	'SyntaxError',
	"#Duplicate parameter name 'X'#i",
);
