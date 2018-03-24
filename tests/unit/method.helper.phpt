<?php

use \Tester\Assert;

use \Smuuf\Primi\Helpers;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\RegexValue;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\InternalArgumentCountException;
use \Smuuf\Primi\InternalUndefinedMethodException;

require __DIR__ . '/../bootstrap.php';

function get_val(Value $v) {
	return $v->getInternalValue();
}

$s = new StringValue("hello there");

//
// Correct invocation.
//

// Test correct invocation.
$args = [new StringValue("there")];
$result = Helpers::invokeValueMethod($s, 'first', $args);
Assert::same(6, get_val($result));

//
// Incorrect invocations.
//

// The helper function should convert any internal/php errors to
// a proper, normal Primi's error exception.

// Test incorrect invocation - too few arguments.
Assert::exception(function() use ($s) {
    $result = Helpers::invokeValueMethod($s, 'first', []);
}, InternalArgumentCountException::class);

// Test incorrect invocation - undefined method.
Assert::exception(function() use ($s) {
    $result = Helpers::invokeValueMethod($s, 'totally_undefined_method');
}, ErrorException::class, '#undefined.*method#i');

// Test incorrect invocation - wrong type of argument.
Assert::exception(function() use ($s) {
    $wrongArgs = [new RegexValue("@[abc]@")];
    $result = Helpers::invokeValueMethod($s, 'first', [$wrongArgs]);
}, ErrorException::class, '#wrong.*arguments#i');
