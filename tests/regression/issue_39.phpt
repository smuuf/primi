<?php

//
// Resolved issue:
//  Unhandled PHP exception when accessing dict items with key which is unhashable #39
// https://github.com/smuuf/primi/issues/39
//

use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Ex\ErrorException;

use \Tester\Assert;

require __DIR__ . "/../bootstrap.php";

$i = new Interpreter;


Assert::noError(fn() => $i->run('x = {(1, 2): true}; x[(1, 2)]'));

assert_uncaught_error(
	fn() => $i->run('x = {(1, 2): true}; x[[1, 2]]'),
	'TypeError',
	'#unhashable type.*list#',
);

assert_uncaught_error(
	fn() => $i->run('x = {(1, 2): true}; x[{"a": "b"}]'),
	'TypeError',
	'#unhashable type.*dict#',
);

assert_uncaught_error(
	fn() => $i->run('x = {(1, 2): true}; x[[1, 2]] = 123'),
	'TypeError',
	'#unhashable type.*list#',
);

assert_uncaught_error(
	fn() => $i->run('x = {(1, 2): true}; x[{"a": "b"}] = 123'),
	'TypeError',
	'#unhashable type.*dict#',
);

