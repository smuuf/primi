<?php

use \Tester\Assert;

use \Smuuf\Primi\Ex\EngineError;
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

Assert::exception(function() {
	AbstractValue::build('whatever');
}, EngineError::class, '#does not implement factory method#');

Assert::same(NullValue::TYPE, AbstractValue::buildAuto(null)::TYPE);

Assert::same(NumberValue::TYPE, AbstractValue::buildAuto(1)::TYPE);
Assert::same(NumberValue::TYPE, AbstractValue::buildAuto(-1)::TYPE);
Assert::same(NumberValue::TYPE, AbstractValue::buildAuto("0")::TYPE);
Assert::same(NumberValue::TYPE, AbstractValue::buildAuto("+4")::TYPE);
Assert::same(NumberValue::TYPE, AbstractValue::buildAuto(-123)::TYPE);

Assert::same(StringValue::TYPE, AbstractValue::buildAuto("a")::TYPE);
Assert::same(StringValue::TYPE, AbstractValue::buildAuto("")::TYPE);
Assert::same(StringValue::TYPE, AbstractValue::buildAuto("word")::TYPE);
Assert::same(StringValue::TYPE, AbstractValue::buildAuto("-1 squirrels")::TYPE);

Assert::same(BoolValue::TYPE, AbstractValue::buildAuto(true)::TYPE);
Assert::same(BoolValue::TYPE, AbstractValue::buildAuto(false)::TYPE);

Assert::same(ListValue::TYPE, AbstractValue::buildAuto([])::TYPE);
Assert::same(ListValue::TYPE, AbstractValue::buildAuto([1])::TYPE);

Assert::same(DictValue::TYPE, AbstractValue::buildAuto([4 => 'a', 5 => 'b'])::TYPE);
Assert::same(DictValue::TYPE, AbstractValue::buildAuto(['a' => 'x', 'y' => 'z'])::TYPE);

Assert::same(FuncValue::TYPE, AbstractValue::buildAuto(function() {})::TYPE);
Assert::same(FuncValue::TYPE, AbstractValue::buildAuto(function(NumberValue $x, DictValue $y) { return 1; })::TYPE);

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
$v = new DictValue(Func::php_array_to_dict_pairs([
	'aaa' => 1, // Will be converted to AbstractValue behind the scenes.
	'bbb' => AbstractValue::buildAuto("xxx"),
	'ccc' => AbstractValue::buildAuto(false),
	'ddd' => new RegexValue('abc'),
	'___' => new RegexValue('/abc/'),
]));
Assert::same('{"aaa": 1, "bbb": "xxx", "ccc": false, "ddd": rx"abc", "___": rx"/abc/"}', $v->getStringRepr());

