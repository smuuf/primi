<?php

use \Smuuf\Primi\ExtensionHub;
use \Smuuf\Primi\Structures\{
	StringValue,
	NumberValue,
	RegexValue,
	FuncValue,
	ArrayValue,
	BoolValue,
	Value
};
use \Smuuf\Primi\Structures\FnContainer;

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

function get_val(Value $v) {
	return $v->getInternalValue();
}

// Prepare helper objects.
$fns = ExtensionHub::get();
$something = new StringValue("something");
$anything = new StringValue("anything");
$someKey = "some_key";

// Test behaviour of empty array.
$arr = new ArrayValue([]);
Assert::same(0, get_val($fns['array_length']->invoke([$arr])));
Assert::same(false, get_val($fns['array_contains']->invoke([$arr, $something])));

// Test proper exception when accessing non-existing key.
Assert::exception(function() use ($arr) {
	$arr->arrayGet("non_existing_key");
}, \Smuuf\Primi\InternalUndefinedIndexException::class);

// Test working with insertion proxy.
$proxy = $arr->getArrayInsertionProxy($someKey);
Assert::same(0, get_val($fns['array_length']->invoke([$arr])));
Assert::same(false, get_val($fns['array_contains']->invoke([$arr, $anything])));
$proxy->commit($anything);
Assert::same(1, get_val($fns['array_length']->invoke([$arr])));
Assert::same(true, get_val($fns['array_contains']->invoke([$arr, $anything])));

// Test getting and iterating array object iterator.
$result = [];
foreach ($arr->getIterator() as $item) {
	$result[] = get_val($item);
}
Assert::same(["anything"], $result);

// Test deep clone of array object (all inner objects ought to be cloned too).
$cloned = clone $arr;
Assert::notSame($cloned->arrayGet($someKey), $arr->arrayGet($someKey));

//
// Test methods.
//

$arr = new ArrayValue([]);

// Push an item into the array and test stuff.
$fns['array_push']->invoke([$arr, $anything]);
Assert::same(1, get_val($fns['array_length']->invoke([$arr])));
Assert::same(false, get_val($fns['array_contains']->invoke([$arr, $something])));
Assert::same(true, get_val($fns['array_contains']->invoke([$arr, $anything])));

// Pop an item form the array and test stuff.
$fns['array_pop']->invoke([$arr]);
Assert::same(0, get_val($fns['array_length']->invoke([$arr])));
Assert::same(false, get_val($fns['array_contains']->invoke([$arr, $something])));
Assert::same(false, get_val($fns['array_contains']->invoke([$arr, $anything])));

// Prepare helper objects.
$num1 = new NumberValue(1);
$num2 = new NumberValue(2);
$num3 = new NumberValue(3);
$arr = new ArrayValue([$num1, $num2, $num3]);

// Test that getting a random item from that array really does that.
$val = $fns['array_random']->invoke([$arr]);
Assert::true($val === $num1 || $val === $num2 || $val === $num3);

$items = get_val($fns['array_shuffle']->invoke([$arr]));
foreach ($items as $item) {
	Assert::contains($item, get_val($arr));
}

// Test cloning the array.
$copy = $fns['array_copy']->invoke([$arr]);
Assert::notSame($arr, $copy);

// Test that array values were cloned, too (ie. deep copy was performed).
$a1 = get_val($arr);
$a2 = get_val($copy);
foreach ($a1 as $index => $item) {
	Assert::notSame($item, $a2[$index]);
}

//
// Test method: "map"
//

$num1 = new NumberValue(1);
$num2 = new NumberValue(45);
$num3 = new NumberValue(-30);
$arr = new ArrayValue([$num1, $num2, $num3]);

$fn = new FuncValue(FnContainer::buildFromClosure(function ($input) {
	return $input * 2;
}));

$result = get_val($fns['array_map']->invoke([$arr, $fn]));
Assert::type('array', $result);
Assert::same(2, get_val(array_shift($result)));
Assert::same(90, get_val(array_shift($result)));
Assert::same(-60, get_val(array_shift($result)));
