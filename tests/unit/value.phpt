<?php

use \Tester\Assert;

use \Smuuf\Primi\Values\{
	ValueFactory,
	StringValue,
	NumberValue,
	RegexValue,
	DictValue,
	ListValue,
	FuncValue,
	BoolValue
};
use \Smuuf\Primi\Helpers\Func;

require __DIR__ . '/../bootstrap.php';

Assert::same(NumberValue::TYPE, ValueFactory::buildAutomatic(1)::TYPE);
Assert::same(NumberValue::TYPE, ValueFactory::buildAutomatic(-1)::TYPE);
Assert::same(NumberValue::TYPE, ValueFactory::buildAutomatic("0")::TYPE);
Assert::same(NumberValue::TYPE, ValueFactory::buildAutomatic("+4")::TYPE);
Assert::same(NumberValue::TYPE, ValueFactory::buildAutomatic(-123)::TYPE);

Assert::same(StringValue::TYPE, ValueFactory::buildAutomatic("a")::TYPE);
Assert::same(StringValue::TYPE, ValueFactory::buildAutomatic("")::TYPE);
Assert::same(StringValue::TYPE, ValueFactory::buildAutomatic("word")::TYPE);
Assert::same(StringValue::TYPE, ValueFactory::buildAutomatic("-1 squirrels")::TYPE);

Assert::same(BoolValue::TYPE, ValueFactory::buildAutomatic(true)::TYPE);
Assert::same(BoolValue::TYPE, ValueFactory::buildAutomatic(false)::TYPE);

Assert::same(ListValue::TYPE, ValueFactory::buildAutomatic([])::TYPE);
Assert::same(ListValue::TYPE, ValueFactory::buildAutomatic([1])::TYPE);

Assert::same(DictValue::TYPE, ValueFactory::buildAutomatic([4 => 'a', 5 => 'b'])::TYPE);
Assert::same(DictValue::TYPE, ValueFactory::buildAutomatic(['a' => 'x', 'y' => 'z'])::TYPE);

Assert::same(FuncValue::TYPE, ValueFactory::buildAutomatic(function() {})::TYPE);
Assert::same(FuncValue::TYPE, ValueFactory::buildAutomatic(function(NumberValue $x, DictValue $y) { return 1; })::TYPE);

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
$v = new BoolValue(true);
Assert::same('true', $v->getStringRepr());
// Bool: False
$v = new BoolValue(false);
Assert::same('false', $v->getStringRepr());

// Regex 1.
$v = new RegexValue('/abc/');
Assert::same('rx"/abc/"', $v->getStringRepr());
// Regex 2.
$v = new RegexValue('abc');
Assert::same('rx"abc"', $v->getStringRepr());

// List.
$v = new ListValue([
	ValueFactory::buildAutomatic(1),
	ValueFactory::buildAutomatic("xxx"),
	ValueFactory::buildAutomatic(false),
	new RegexValue('abc'),
	new RegexValue('/abc/'),
]);
Assert::same('[1, "xxx", false, rx"abc", rx"/abc/"]', $v->getStringRepr());

// Dict.
$v = new DictValue(Func::php_array_to_dict_pairs([
	'aaa' => 1, // Will be converted to AbstractValue behind the scenes.
	'bbb' => ValueFactory::buildAutomatic("xxx"),
	'ccc' => ValueFactory::buildAutomatic(false),
	'ddd' => new RegexValue('abc'),
	'___' => new RegexValue('/abc/'),
]));
Assert::same('{"aaa": 1, "bbb": "xxx", "ccc": false, "ddd": rx"abc", "___": rx"/abc/"}', $v->getStringRepr());

