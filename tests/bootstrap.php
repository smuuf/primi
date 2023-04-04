<?php

use Tester\Assert;

use Smuuf\Primi\Ex\UncaughtError;
use Smuuf\Primi\Ex\PiggybackException;

require __DIR__ . '/../vendor/autoload.php';

\Tester\Environment::setup();
\Tester\Dumper::$maxLength = 200;

function assert_piggyback_exception(callable $fn, string $typeName, string $regex): void {

	/** @var PiggybackException */
	$ex = Assert::exception($fn, PiggybackException::class);
	Assert::same($typeName, $ex->excType->getName());
	Assert::match($regex, $ex->args[0]);

}

function assert_uncaught_error(callable $fn, string $typeName, string $regex): void {

	/** @var UncaughtError */
	$ex = Assert::exception($fn, UncaughtError::class);
	Assert::same($typeName, $ex->thrownException->exception->getTypeName());
	Assert::match($regex, $ex->thrownException->exception->attrGet('args')->getStringRepr());

}
