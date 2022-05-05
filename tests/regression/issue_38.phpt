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

Assert::exception(fn() => $i->run('dict("ahoj")'), ErrorException::class, '#less than two#');
Assert::exception(fn() => $i->run('dict([[]])'), ErrorException::class, '#less than two#');
Assert::exception(fn() => $i->run('dict([[1]])'), ErrorException::class, '#less than two#');
Assert::exception(fn() => $i->run('dict([[1, 2, 3]])'), ErrorException::class, '#more than two#');

Assert::noError(fn() => $i->run('dict([[1, 2]])'));
Assert::noError(fn() => $i->run('dict([[1, 2], (3, 4)])'));
