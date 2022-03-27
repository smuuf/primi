<?php

use \Smuuf\Primi\Config;
use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\Ex\ErrorException;

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$sandboxConfig = new Config;
$sandboxConfig->setSandboxMode(true);

$i = new Interpreter($sandboxConfig);
Assert::exception(
	fn() => $i->run('import std.runtime'),
	ErrorException::class, '#Access to module disabled when in sandbox#'
);

$fullConfig = new Config;
$fullConfig->setSandboxMode(false);

$i = new Interpreter($fullConfig);
Assert::noError(fn() => $i->run('import std.runtime'));
