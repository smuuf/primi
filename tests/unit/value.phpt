<?php

use \Tester\Assert;
use \Smuuf\Primi\Structures\{
	Value,
	StringValue,
	NumberValue,
	RegexValue,
	ArrayValue,
	BoolValue
};

require __DIR__ . '/../bootstrap.php';

Assert::same(NumberValue::TYPE, Value::buildAutomatic(1)::TYPE);
Assert::same(NumberValue::TYPE, Value::buildAutomatic(-1)::TYPE);
Assert::same(NumberValue::TYPE, Value::buildAutomatic("0")::TYPE);
Assert::same(NumberValue::TYPE, Value::buildAutomatic("+4")::TYPE);
Assert::same(NumberValue::TYPE, Value::buildAutomatic(-123)::TYPE);

Assert::same(StringValue::TYPE, Value::buildAutomatic("a")::TYPE);
Assert::same(StringValue::TYPE, Value::buildAutomatic("")::TYPE);
Assert::same(StringValue::TYPE, Value::buildAutomatic("word")::TYPE);
Assert::same(StringValue::TYPE, Value::buildAutomatic("-1 squirrels")::TYPE);

Assert::same(BoolValue::TYPE, Value::buildAutomatic(true)::TYPE);
Assert::same(BoolValue::TYPE, Value::buildAutomatic(false)::TYPE);

Assert::same(ArrayValue::TYPE, Value::buildAutomatic([])::TYPE);
Assert::same(ArrayValue::TYPE, Value::buildAutomatic([1])::TYPE);

//
// Getting string representation of values.
//

// String - will have double quotes around it.
$v = new StringValue("hel\"lo!");
Assert::same('"hel\"lo!"', $v->getStringValue());

// Number.
$v = new NumberValue("123");
Assert::same('123', $v->getStringValue());

// Bool: True.
$v = new BoolValue(true);
Assert::same('true', $v->getStringValue());

// Bool: False
$v = new BoolValue(false);
Assert::same('false', $v->getStringValue());

// Regex.
$v = new RegexValue('/abc/');
Assert::same('/abc/u', $v->getStringValue());

// Array.
$v = new ArrayValue([
	Value::buildAutomatic(1),
	Value::buildAutomatic("xxx"),
	Value::buildAutomatic(false),
	new RegexValue('/abc/'),
]);
Assert::same('[0: 1, 1: "xxx", 2: false, 3: /abc/u]', $v->getStringValue());
