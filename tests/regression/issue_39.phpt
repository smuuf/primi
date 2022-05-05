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
Assert::exception(fn() => $i->run('x = {(1, 2): true}; x[[1, 2]]'), ErrorException::class, '#unhashable type.*list#');
Assert::exception(fn() => $i->run('x = {(1, 2): true}; x[{"a": "b"}]'), ErrorException::class, '#unhashable type.*dict#');
Assert::exception(fn() => $i->run('x = {(1, 2): true}; x[[1, 2]] = 123'), ErrorException::class, '#unhashable type.*list#');
Assert::exception(fn() => $i->run('x = {(1, 2): true}; x[{"a": "b"}] = 123'), ErrorException::class, '#unhashable type.*dict#');
