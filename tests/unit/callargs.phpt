<?php

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Structures\CallArgs;
use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// No args nor kwargs at all.
Assert::noError(
	fn() => new CallArgs()
);

// Only keyword args.
Assert::noError(
	fn() => new CallArgs([], ['a' => 1])
);

// Only positional args.
Assert::noError(
	fn() => new CallArgs([1, 2], [])
);

// Positional args must be passed as a list array.
Assert::exception(function() {
	return new CallArgs(['a' => 1, 2], []);
}, EngineError::class, '#positional.*list#i');

// Positional args must be passed as a list array #2.
Assert::exception(function() {
	return new CallArgs(['a' => 1, 2], ['hello' => 123]);
}, EngineError::class, '#positional.*list#i');

//
// Combining CallArgs objects. #1
//

$argsA = new CallArgs([1, 2, 3], ['hello_a' => 123]);
$argsB = new CallArgs([4, 5], ['hello_a' => 456]);
$combined = $argsA->withExtra($argsB);
Assert::same([1, 2, 3, 4, 5], $combined->getArgs());
Assert::same(['hello_a' => 456], $combined->getKwargs());

//
// Combining CallArgs objects. #2
//

$argsA = new CallArgs([1], ['hello_a' => 123]);
$argsB = new CallArgs([4, 5], ['hello_b' => 456, 'something' => true]);
$combined = $argsA->withExtra($argsB);
Assert::same([1, 4, 5], $combined->getArgs());
Assert::same(
	['hello_a' => 123, 'hello_b' => 456, 'something' => true],
	$combined->getKwargs()
);
