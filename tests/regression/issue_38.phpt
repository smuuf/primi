<?php

//
// Resolved issue:
// Undefined offset error when passing string to dict constructor #38
// https://github.com/smuuf/primi/issues/38
//

use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Ex\ErrorException;

use \Tester\Assert;

require __DIR__ . "/../bootstrap.php";

$i = new Interpreter;

assert_uncaught_error(
	fn() => $i->run('dict("ahoj")'),
	'TypeError',
	'#less than two#',
);
assert_uncaught_error(
	fn() => $i->run('dict([[]])'),
	'TypeError',
	'#less than two#',
);
assert_uncaught_error(
	fn() => $i->run('dict([[1]])'),
	'TypeError',
	'#less than two#',
);
assert_uncaught_error(
	fn() => $i->run('dict([[1, 2, 3]])'),
	'TypeError',
	'#more than two#',
);

Assert::noError(fn() => $i->run('dict([[1, 2]])'));
Assert::noError(fn() => $i->run('dict([[1, 2], (3, 4)])'));
