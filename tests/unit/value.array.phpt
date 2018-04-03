<?php

use \Tester\Assert;
use \Smuuf\Primi\Structures\{
	StringValue,
	NumberValue,
	RegexValue,
	FuncValue,
	ArrayValue,
	BoolValue,
	Value
};
use \Smuuf\Primi\Structures\FunctionContainer;

require __DIR__ . '/../bootstrap.php';

function get_val(Value $v) {
	return $v->getInternalValue();
}

// Prepare helper objects.
$something = new StringValue("something");
$anything = new StringValue("anything");
$someKey = new StringValue("some_key");

// Test behaviour of empty array.
$arr = new ArrayValue([]);
Assert::same(0, get_val($arr->propLength()));
Assert::same(false, get_val($arr->call('contains', [$something])));

// Test proper exception when accessing non-existing key.
Assert::exception(function() use ($arr) {
	$arr->dereference(new StringValue("non_existing_key"));
}, \Smuuf\Primi\InternalUndefinedIndexException::class);

// Test working with insertion proxy.
$proxy = $arr->getInsertionProxy($someKey);
Assert::same(0, get_val($arr->propLength()));
Assert::same(false, get_val($arr->call('contains', [$anything])));
$proxy->commit($anything);
Assert::same(1, get_val($arr->propLength()));
Assert::same(true, get_val($arr->call('contains', [$anything])));

// Test getting and iterating array object iterator.
$result = [];
foreach ($arr->getIterator() as $item) {
	$result[] = get_val($item);
}
Assert::same(["anything"], $result);

// Test deep clone of array object (all inner objects ought to be cloned too).
$cloned = clone $arr;
Assert::notSame($cloned->dereference($someKey), $arr->dereference($someKey));

//
// Test methods.
//

$arr = new ArrayValue([]);

// Push an item into the array and test stuff.
$arr->call('push', [$anything]);
Assert::same(1, get_val($arr->propLength()));
Assert::same(false, get_val($arr->call('contains', [$something])));
Assert::same(true, get_val($arr->call('contains', [$anything])));

// Pop an item form the array and test stuff.
$arr->call('pop');
Assert::same(0, get_val($arr->propLength()));
Assert::same(false, get_val($arr->call('contains', [$something])));
Assert::same(false, get_val($arr->call('contains', [$anything])));

// Prepare helper objects.
$num1 = new NumberValue(1);
$num2 = new NumberValue(2);
$num3 = new NumberValue(3);
$arr = new ArrayValue([$num1, $num2, $num3]);

// Test that getting a random item from that array really does that.
$val = $arr->call('random');
Assert::true($val === $num1 || $val === $num2 || $val === $num3);

$items = get_val($arr->call('shuffle'));
foreach ($items as $item) {
	Assert::contains($item, get_val($arr));
}

// Test cloning the array.
$copy = $arr->call('copy');
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

$fn = new FuncValue(FunctionContainer::buildNative(function ($input) {
	return $input * 2;
}));

$result = get_val($arr->call('map', [$fn]));
Assert::type('array', $result);
Assert::same(2, get_val(array_shift($result)));
Assert::same(90, get_val(array_shift($result)));
Assert::same(-60, get_val(array_shift($result)));
