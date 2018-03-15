<?php

use \Tester\Assert;
use \Smuuf\Primi\Structures\{
	StringValue,
	NumberValue,
	RegexValue,
	ArrayValue,
	BoolValue,
	Value
};

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
Assert::notSame(
	$cloned->dereference($someKey),
	$arr->dereference($someKey)
);
