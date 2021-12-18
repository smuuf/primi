<?php

use \Tester\Assert;

use \Smuuf\Primi\Values\{
	AbstractValue,
	StringValue,
	NumberValue,
	RegexValue,
	DictValue,
	ListValue,
	FuncValue,
	BoolValue,
	NullValue
};
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;

require __DIR__ . '/../bootstrap.php';

Assert::type(NullValue::class, AbstractValue::buildAuto(null));

Assert::type(NumberValue::class, AbstractValue::buildAuto(1));
Assert::type(NumberValue::class, AbstractValue::buildAuto(-1));
Assert::type(NumberValue::class, AbstractValue::buildAuto("0"));
Assert::type(NumberValue::class, AbstractValue::buildAuto("+4"));
Assert::type(NumberValue::class, AbstractValue::buildAuto(-123));

Assert::type(StringValue::class, AbstractValue::buildAuto("a"));
Assert::type(StringValue::class, AbstractValue::buildAuto(""));
Assert::type(StringValue::class, AbstractValue::buildAuto("word"));
Assert::type(StringValue::class, AbstractValue::buildAuto("-1 squirrels"));

Assert::type(BoolValue::class, AbstractValue::buildAuto(true));
Assert::type(BoolValue::class, AbstractValue::buildAuto(false));

Assert::type(ListValue::class, AbstractValue::buildAuto([]));
Assert::type(ListValue::class, AbstractValue::buildAuto([1]));

Assert::type(DictValue::class, AbstractValue::buildAuto([4 => 'a', 5 => 'b']));
Assert::type(DictValue::class, AbstractValue::buildAuto(['a' => 'x', 'y' => 'z']));

Assert::type(FuncValue::class, AbstractValue::buildAuto(function() {}));
Assert::type(FuncValue::class, AbstractValue::buildAuto(function(NumberValue $x, DictValue $y) { return 1; }));

//
// Getting string representation of values.
//

// String - 'repr' will have double quotes around it.
$v = new StringValue("hel\"lo!");
Assert::same('"hel\"lo!"', $v->getStringRepr());

// String - 'value' will NOT have double quotes around it.
$v = new StringValue("hel\"lo!");
Assert::same('hel"lo!', $v->getStringValue());

// Number.
$v = new NumberValue("123");
Assert::same('123', $v->getStringRepr());
// Number which floats.
$v = new NumberValue("123.789");
Assert::same('123.789', $v->getStringRepr());
// Number which floats 2.
$v = new NumberValue("000123.789");
Assert::same('123.789', $v->getStringRepr());

// Bool: True.
$v = Interned::bool(true);
Assert::same('true', $v->getStringRepr());
// Bool: False
$v = Interned::bool(false);
Assert::same('false', $v->getStringRepr());

// Regex 1.
$v = new RegexValue('/abc/');
Assert::same('rx"/abc/"', $v->getStringRepr());
// Regex 2.
$v = new RegexValue('abc');
Assert::same('rx"abc"', $v->getStringRepr());

// List.
$v = new ListValue([
	AbstractValue::buildAuto(1),
	AbstractValue::buildAuto("xxx"),
	AbstractValue::buildAuto(false),
	new RegexValue('abc'),
	new RegexValue('/abc/'),
]);
Assert::same('[1, "xxx", false, rx"abc", rx"/abc/"]', $v->getStringRepr());

// Dict.
$v = new DictValue(Func::array_to_couples([
	'aaa' => 1, // Will be converted to AbstractValue behind the scenes.
	'bbb' => AbstractValue::buildAuto("xxx"),
	'ccc' => AbstractValue::buildAuto(false),
	'ddd' => new RegexValue('abc'),
	'___' => new RegexValue('/abc/'),
]));
Assert::same('{"aaa": 1, "bbb": "xxx", "ccc": false, "ddd": rx"abc", "___": rx"/abc/"}', $v->getStringRepr());

