<?php

use \Smuuf\Primi\Config;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Drivers\VoidIoDriver;

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$sandboxConfig = new Config;
$sandboxConfig->setSandboxMode(true);
$sandboxConfig->setStdIoDriver(new VoidIoDriver);

$i = new Interpreter($sandboxConfig);

assert_uncaught_error(
	fn() => $i->run('import std.runtime'),
	'RuntimeError',
	'#Access.*forbidden.*sandbox#',
);

$fullConfig = new Config;
$fullConfig->setSandboxMode(false);
$fullConfig->setStdIoDriver(new VoidIoDriver);

$i = new Interpreter($fullConfig);
Assert::noError(fn() => $i->run('import std.runtime'));
