<?php

use \Smuuf\Primi\Config;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Ex\ErrorException;
use \Smuuf\Primi\Drivers\VoidIoDriver;

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$sandboxConfig = new Config;
$sandboxConfig->setSandboxMode(true);
$sandboxConfig->setStdIoDriver(new VoidIoDriver);

$i = new Interpreter($sandboxConfig);
Assert::exception(
	fn() => $i->run('import std.runtime'),
	ErrorException::class, '#Access.*forbidden.*sandbox#'
);

$fullConfig = new Config;
$fullConfig->setSandboxMode(false);
$fullConfig->setStdIoDriver(new VoidIoDriver);

$i = new Interpreter($fullConfig);
Assert::noError(fn() => $i->run('import std.runtime'));
