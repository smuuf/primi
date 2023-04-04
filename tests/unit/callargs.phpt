<?php

declare(strict_types=1);

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Structures\CallArgs;
use \Smuuf\Primi\Values\DictValue;
use \Tester\Assert;
use Tester\Expect;

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
// Prefixing CallArgs objects. #1
//

$args = new CallArgs([1, 2, 3], ['hello_a' => 123]);
$prefix = [999];
$combined = $args->withPrefixed($prefix);
Assert::same([999, 1, 2, 3], $combined->getArgs());
Assert::same(['hello_a' => 123], $combined->getKwargs());

//
// Prefixing CallArgs objects. #2
//

$args = new CallArgs([1]);
$prefix = [999, 888];
$combined = $args->withPrefixed($prefix);
Assert::same([999, 888, 1], $combined->getArgs());
Assert::same([], $combined->getKwargs());

//
// Prefixing CallArgs objects. #2
//

$args = new CallArgs();
$prefix = [999, 888];
$combined = $args->withPrefixed($prefix);
Assert::same([999, 888], $combined->getArgs());
Assert::same([], $combined->getKwargs());

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

//
// Extracting args.
//

// This is ok.

$args = new CallArgs(
	[1, 'abc', 'xyz'], // Positional args.
	['kw_a' => 'aaa', 'kw_b' => 'bbb'] // Keyword args.
);
$result = $args->extract(['a', 'b', 'c', 'kw_a', 'kw_b']);
Assert::equal([
	'a' => 1,
	'b' => 'abc',
	'c' => 'xyz',
	'kw_a' => 'aaa',
	'kw_b' => 'bbb',
], $result);

// Here are some unexpected extra kwargs and extraction is kwargs collector
// is not specified via some '**whatever' kwarg collector.

assert_piggyback_exception(
	function() {

		$args = new CallArgs(
			[1, 'abc', 'xyz'], // Positional args.
			['kw_a' => 'aaa', 'kw_b' => 'bbb', 'kw_c' => 'ccc', 'kw_d' => 'ddd'] // Keyword args.
		);
		$args->extract(['a', 'b', 'c', 'kw_a', 'kw_b']);

	},
	'TypeError',
	'#unexpected.*keyword.*kw_c#i',
);

// Test that ::extract() is used to extract 2 positional arguments but there
// are in fact 3 arguments - so there were too many positional arguments - and
// that's an error.

assert_piggyback_exception(
	function() {

		$args = new CallArgs(
			[1, 'abc', 'xyz'], // Positional args.
		);
		$args->extract(['a', 'b']);

	},
	'TypeError',
	'#Too many positional arguments#i',
);

// Here are some unexpected extra kwargs but kwargs collector is specified.
// via some '**whatever' kwarg collector.

$args = new CallArgs(
	[1, 'abc', 'xyz'], // Positional args.
	['kw_a' => 'aaa', 'kw_b' => 'bbb', 'kw_c' => 'ccc', 'kw_d' => 'ddd'] // Keyword args.
);
$result = $args->extract(['a', 'b', 'c', 'kw_a', 'kw_b', '**rest']);
Assert::equal([
	'a' => 1,
	'b' => 'abc',
	'c' => 'xyz',
	'kw_a' => 'aaa',
	'kw_b' => 'bbb',
	'rest' => Expect::type(DictValue::class),
], $result);

// Here some arguments are missing and that's not ok.
assert_piggyback_exception(
	function() {
		$args = new CallArgs(
			[1, 2, 3], // Positional args.
			['kw_a' => 'aaa', 'kw_b' => 'bbb'] // Keyword args.
		);
		$_ = $args->extract(['a', 'b', 'c', 'kw_a', 'kw_b', 'kw_c']);
	},
	'TypeError',
	'#missing.*required.*argument.*kw_c#i',
);

// Here some arguments are missing, but it's ok, because we specify an
// optional list of optional arguments.

$args = new CallArgs(
	[1, 2, 3], // Positional args.
	['kw_a' => 'aaa', 'kw_b' => 'bbb'] // Keyword args.
);
$result = $args->extract(['a', 'b', 'c', 'kw_a', 'kw_b', 'kw_c'], ['kw_c']);
Assert::equal([
	'a' => 1,
	'b' => 2,
	'c' => 3,
	'kw_a' => 'aaa',
	'kw_b' => 'bbb',
], $result);

// Throwing exception if ::extractPositional() is used but there are also
// kwargs.

assert_piggyback_exception(
	function() {

		$args = new CallArgs(
			[1, 'abc', 'xyz'], // Positional args.
			['kw_a' => 'aaa', 'kw_b' => 'bbb', 'kw_c' => 'ccc', 'kw_d' => 'ddd'] // Keyword args.
		);
		[$a, $b, $c] = $args->extractPositional(3);

	},
	'TypeError',
	"#Unexpected keyword argument 'kw_a'#i",
);

// Throwing exception if ::extractPositional() is used but client expects more
// args than there are present.

assert_piggyback_exception(
	function() {

		$args = new CallArgs([1, 'abc', 'def']);
		[$a, $b] = $args->extractPositional(2);

	},
	'TypeError',
	"#Expected maximum of 2 arguments#i",
);

// Throwing exception if ::extractPositional() is used with number of optional
// arguments specified but client expected more mandatory arguments than there
// were present.

assert_piggyback_exception(
	function() {

		$args = new CallArgs([1, 'abc', 'def']);
		[$a, $b, $c, $d, $e] = $args->extractPositional(5, optional: 1);

	},
	'TypeError',
	"#Expected at least 4 arguments#i", // Expected up to 5 but minimum of 4 (which is 5 minus 1) args.
);

// Test that trying to extract more arguments than there were present - but
// the not-present arguments were optional - correctly returns missing arguments
// as null.

$args = new CallArgs([1, 'abc', 'def']);
[$a, $b, $c, $d, $e] = $args->extractPositional(5, optional: 2);
Assert::same(1, $a);
Assert::same('abc', $b);
Assert::same('def', $c);
Assert::same(null, $d);
Assert::same(null, $e);
