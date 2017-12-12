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

