<?php

use \Tester\Assert;

use \Smuuf\Primi\Config;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\InterpreterServices;
use \Smuuf\Primi\Ex\ArgumentCountError;
use \Smuuf\Primi\Structures\FnContainer;
use \Smuuf\Primi\Values\{
	FuncValue,
	NumberValue,
	AbstractValue,
};

require __DIR__ . '/../bootstrap.php';

function get_val(AbstractValue $v) {
	return $v->getInternalValue();
}

$ctx = new Context(new InterpreterServices(Config::buildDefault()));

$one = new NumberValue(1);
$two = new NumberValue(2);
$three = new NumberValue(3);
$five = new NumberValue(5);

//
// Function invocation.
//

// Primi functions created from callables can be only created from callables
// that typehint its parameters as Primi's AbstractValue class or its descendants.

$closure = function(NumberValue $a, NumberValue $b) {
	return new NumberValue($a->getInternalValue() * $b->getInternalValue() ** 2);
};

// Create Primi function from a native PHP function.
// This directly returns a Primi value. (Kind of optional low-levelness.)
$fn = new FuncValue(FnContainer::buildFromClosure($closure));

Assert::same("4", get_val($fn->invoke($ctx, [$one, $two])));
Assert::same("45", get_val($fn->invoke($ctx, [$five, $three])));

//
// Bound native function error handling.
//

// No arguments (but expected some).
Assert::exception(function() use ($fn, $ctx) {
	$fn->invoke($ctx, []);
}, ArgumentCountError::class);

// Too many arguments (expected less) - valid. Allow it.
Assert::noError(function() use ($fn, $ctx, $one, $two, $three) {
	$fn->invoke($ctx, [$one, $two, $three]);
});
