<?php

use \Tester\Assert;

use \Smuuf\Primi\ExtensionHub;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\{
	Value,
	StringValue,
	NumberValue,
	RegexValue,
	DictValue,
	ListValue,
	BoolValue
};

require __DIR__ . '/../bootstrap.php';

function get_val(Value $v) {
	return $v->getInternalValue();
}

$fns = ExtensionHub::get();

$integer = new NumberValue("1");
$biggerInteger = new NumberValue("20");
$posInteger = new NumberValue("+1");
$negInteger = new NumberValue("-1");
$integeryFloat = new NumberValue("1.0");
$posIntegeryFloat = new NumberValue("+1.0");
$negIntegeryFloat = new NumberValue("-1.0");
$float = new NumberValue("2.3");
$posFloat = new NumberValue("+2.3");
$negFloat = new NumberValue("-2.3");
$zero = new NumberValue("0");
$posZero = new NumberValue("+0");
$negZero = new NumberValue("-0");
$posZeroFloat = new NumberValue("+0.0");
$negZeroFloat = new NumberValue("-0.0");

//
// Test int vs float detection.
//

// Test that various input correcly decide which is int and which is float.
Assert::type('int', get_val($integer));
Assert::type('int', get_val($posInteger));
Assert::type('int', get_val($negInteger));
Assert::type('float', get_val($integeryFloat));
Assert::type('float', get_val($posIntegeryFloat));
Assert::type('float', get_val($negIntegeryFloat));
Assert::type('float', get_val($float));
Assert::type('float', get_val($posFloat));
Assert::type('float', get_val($negFloat));
Assert::type('int', get_val($zero));
Assert::type('int', get_val($posZero));
Assert::type('int', get_val($negZero));
Assert::type('float', get_val($posZeroFloat));
Assert::type('float', get_val($negZeroFloat));

//
// Numeric string detection.
//

// Test correct detection of "numeric" string.
Assert::true(Func::is_numeric("1"));
Assert::true(Func::is_numeric("1.2"));
Assert::true(Func::is_numeric("0.0"));
Assert::true(Func::is_numeric("-0.0"));
Assert::true(Func::is_numeric("-1.2"));
Assert::true(Func::is_numeric("-1"));
Assert::false(Func::is_numeric("hell no"));
Assert::false(Func::is_numeric("not 1"));
Assert::false(Func::is_numeric("1 owl"));
Assert::false(Func::is_numeric("2 2"));
Assert::true(Func::is_numeric("+0.0"));
Assert::false(Func::is_numeric("+-1"));

//
// Test addition.
//

// Addition with a negative 0 constructed from string.
Assert::same(1, get_val($integer->doAddition(new NumberValue("-0"))));
// Addition with a negative 5 constructed from string.
Assert::same(-4, get_val($integer->doAddition(new NumberValue("-5"))));
// Addition with a proper zero Number value.
Assert::same(1, get_val($integer->doAddition(new NumberValue(0))));
// Addition with a proper Number one.
Assert::same(2, get_val($integer->doAddition(new NumberValue(1))));
// Addition with a proper negative Number.
Assert::same(-122, get_val($integer->doAddition(new NumberValue(-123))));

// Addition with unsupported formats will result in type error.
Assert::null($integer->doAddition(new StringValue("4")));
Assert::null($integer->doAddition(new DictValue([])));
Assert::null($integer->doAddition(new BoolValue(true)));
Assert::null($integer->doAddition(new RegexValue("/[abc]/")));

//
// Test subtraction.
//

Assert::same(1, get_val($integer->doSubtraction(new NumberValue("-0"))));
Assert::same(6, get_val($integer->doSubtraction(new NumberValue("-5"))));
Assert::same(1, get_val($integer->doSubtraction(new NumberValue(0))));
Assert::same(0, get_val($integer->doSubtraction(new NumberValue(1))));
Assert::same(124, get_val($integer->doSubtraction(new NumberValue(-123))));

// Subtraction with unsupported formats will result in type error.
Assert::null($integer->doSubtraction(new StringValue("1")));
Assert::null($integer->doSubtraction(new DictValue([])));
Assert::null($integer->doSubtraction(new BoolValue(false)));
Assert::null($integer->doSubtraction(new RegexValue("/[abc]/")));

//
// Test multiplication.
//

// Multiplication with numbers.
Assert::same(0, get_val($float->doMultiplication(new NumberValue("-0"))));
Assert::same(-11.5, get_val($float->doMultiplication(new NumberValue("-5"))));
Assert::same(0, get_val($float->doMultiplication(new NumberValue(0))));
Assert::same(2.3, get_val($float->doMultiplication(new NumberValue(1))));
Assert::same(-282.9, get_val($float->doMultiplication(new NumberValue(-123))));

// Multiplication by a string.
// Number X String is not supported by Number, but
// String X Number is supported.
$string = new StringValue(" _ěšč");
$result = $biggerInteger->doMultiplication($string);
Assert::null($result);
$result = get_val($string->doMultiplication($biggerInteger));
Assert::same(" _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč", $result);

// Multiplication with unsupported formats will result in type error.
Assert::null($integer->doMultiplication(new StringValue(" b")));
Assert::null($posFloat->doMultiplication(new StringValue(" a")));
Assert::null($negFloat->doMultiplication(new StringValue(" b")));
Assert::null($integer->doMultiplication(new DictValue([])));
Assert::null($integer->doMultiplication(new BoolValue(false)));
Assert::null($integer->doMultiplication(new RegexValue("/[abc]/")));

//
// Test division.
//

Assert::exception(function() use ($float) {
	$float->doDivision(new NumberValue("-0"));
}, \Smuuf\Primi\Ex\RuntimeError::class, '#Division.*zero#');
Assert::exception(function() use ($integer) {
	$integer->doDivision(new NumberValue(0));
}, \Smuuf\Primi\Ex\RuntimeError::class, '#Division.*zero#');
Assert::same(-0.46, get_val($float->doDivision(new NumberValue("-5"))));
Assert::same(2.3, get_val($float->doDivision(new NumberValue(1))));
Assert::same(-1.15, get_val($float->doDivision(new NumberValue(-2))));

// Subtaction with unsupported formats will result in type error.
Assert::null($integer->doDivision(new StringValue("1")));
Assert::null($integer->doDivision(new ListValue([])));
Assert::null($integer->doDivision(new BoolValue(false)));
Assert::null($integer->doDivision(new RegexValue("/[abc]/")));

//
// Test comparison.
//

$tmp = $integer->isEqualTo(new NumberValue("-1"));
Assert::false($tmp);

$tmp = $integer->isEqualTo(new NumberValue("-1"));
Assert::true(!$tmp);

$tmp = $integer->isEqualTo(new NumberValue("1"));
Assert::true($tmp);

$tmp = $integer->isEqualTo(new NumberValue("1.0"));
Assert::true($tmp);

$tmp = $integer->isEqualTo(new NumberValue("2"));
Assert::true(!$tmp);

$tmp = $float->hasRelationTo(">", new NumberValue("2"));
Assert::true($tmp);

$tmp = $float->hasRelationTo("<", new NumberValue("2.3"));
Assert::false($tmp);

$tmp = $float->hasRelationTo(">=", new NumberValue("2.31"));
Assert::false($tmp);

$tmp = $float->hasRelationTo("<=", new NumberValue("2.31"));
Assert::true($tmp);

//
// Methods...
//

$tmp = $fns['number_abs']->invoke([$integer]);
Assert::same(1, get_val($tmp));
$tmp = $fns['number_abs']->invoke([$biggerInteger]);
Assert::same(20, get_val($tmp));
$tmp = $fns['number_abs']->invoke([$posFloat]);
Assert::same(2.3, get_val($tmp));
$tmp = $fns['number_abs']->invoke([$negFloat]);
Assert::same(2.3, get_val($tmp));

$tmp = $fns['number_sqrt']->invoke([$integer]);
Assert::type('int',  get_val($tmp));
$tmp = $fns['number_pow']->invoke([$integer, new NumberValue(4)]);
Assert::type('int',  get_val($tmp));
$tmp = $fns['number_sin']->invoke([$integer]);
Assert::type('float',  get_val($tmp));
$tmp = $fns['number_cos']->invoke([$integer]);
Assert::type('float',  get_val($tmp));
$tmp = $fns['number_tan']->invoke([$integer]);
Assert::type('float',  get_val($tmp));
$tmp = $fns['number_atan']->invoke([$integer]);
Assert::type('float',  get_val($tmp));
$tmp = $fns['number_ceil']->invoke([$integer]);
Assert::type('int',  get_val($tmp));
$tmp = $fns['number_floor']->invoke([$integer]);
Assert::type('int',  get_val($tmp));
$tmp = $fns['number_round']->invoke([$integer]);
Assert::type('int',  get_val($tmp));

$tmp = $fns['number_sqrt']->invoke([$float]);
Assert::type('float',  get_val($tmp));
$tmp = $fns['number_pow']->invoke([$float, new NumberValue(4)]);
Assert::type('float',  get_val($tmp));
$tmp = $fns['number_sin']->invoke([$float]);
Assert::type('float',  get_val($tmp));
$tmp = $fns['number_cos']->invoke([$float]);
Assert::type('float',  get_val($tmp));
$tmp = $fns['number_tan']->invoke([$float]);
Assert::type('float',  get_val($tmp));
$tmp = $fns['number_atan']->invoke([$float]);
Assert::type('float',  get_val($tmp));
$tmp = $fns['number_ceil']->invoke([$float]);
Assert::type('int',  get_val($tmp));
$tmp = $fns['number_floor']->invoke([$float]);
Assert::type('int',  get_val($tmp));
$tmp = $fns['number_round']->invoke([$float]);
Assert::type('int',  get_val($tmp));
