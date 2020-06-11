<?php

use \Smuuf\Primi\ExtensionHub;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\{
	Value,
	StringValue,
	NumberValue,
	RegexValue,
	DictValue,
	ListValue,
	BoolValue
};

use \Tester\Assert;

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
Assert::true(Common::isNumeric("1"));
Assert::true(Common::isNumeric("1.2"));
Assert::true(Common::isNumeric("0.0"));
Assert::true(Common::isNumeric("-0.0"));
Assert::true(Common::isNumeric("-1.2"));
Assert::true(Common::isNumeric("-1"));
Assert::false(Common::isNumeric("hell no"));
Assert::false(Common::isNumeric("not 1"));
Assert::false(Common::isNumeric("1 owl"));
Assert::false(Common::isNumeric("2 2"));
Assert::true(Common::isNumeric("+0.0"));
Assert::false(Common::isNumeric("+-1"));

//
// Test addition.
//

// Addition with a negative 0 constructed from string.
Assert::same(1, $integer->doAddition(new NumberValue("-0"))->getInternalValue());
// Addition with a negative 5 constructed from string.
Assert::same(-4, $integer->doAddition(new NumberValue("-5"))->getInternalValue());
// Addition with a proper zero Number value.
Assert::same(1, $integer->doAddition(new NumberValue(0))->getInternalValue());
// Addition with a proper Number one.
Assert::same(2, $integer->doAddition(new NumberValue(1))->getInternalValue());
// Addition with a proper negative Number.
Assert::same(-122, $integer->doAddition(new NumberValue(-123))->getInternalValue());

// Addition with unsupported formats will result in type error.
Assert::exception(function() use ($integer) {
	$integer->doAddition(new StringValue("4"));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doAddition(new ArrayValue([]));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doAddition(new BoolValue(true));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doAddition(new RegexValue("/[abc]/"));
}, \TypeError::class);

//
// Test subtraction.
//

Assert::same(1, $integer->doSubtraction(new NumberValue("-0"))->getInternalValue());
Assert::same(6, $integer->doSubtraction(new NumberValue("-5"))->getInternalValue());
Assert::same(1, $integer->doSubtraction(new NumberValue(0))->getInternalValue());
Assert::same(0, $integer->doSubtraction(new NumberValue(1))->getInternalValue());
Assert::same(124, $integer->doSubtraction(new NumberValue(-123))->getInternalValue());

// Subtraction with unsupported formats will result in type error.
Assert::exception(function() use ($integer) {
	$integer->doSubtraction(new StringValue("1"));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doSubtraction(new ArrayValue([]));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doSubtraction(new BoolValue(false));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doSubtraction(new RegexValue("/[abc]/"));
}, \TypeError::class);

//
// Test multiplication.
//

// Multiplication with numbers.
Assert::same(0, $float->doMultiplication(new NumberValue("-0"))->getInternalValue());
Assert::same(-11.5, $float->doMultiplication(new NumberValue("-5"))->getInternalValue());
Assert::same(0, $float->doMultiplication(new NumberValue(0))->getInternalValue());
Assert::same(2.3, $float->doMultiplication(new NumberValue(1))->getInternalValue());
Assert::same(-282.9, $float->doMultiplication(new NumberValue(-123))->getInternalValue());

// Multiplication by a string.
$result = $biggerInteger->doMultiplication(new StringValue(" "))->getInternalValue();
Assert::same("                    ", $result);
$result = $biggerInteger->doMultiplication(new StringValue(" _ěšč"))->getInternalValue();
Assert::same(" _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč", $result);

// Multiplication with unsupported formats will result in type error.
Assert::exception(function() use ($posFloat) {
	$posFloat->doMultiplication(new StringValue(" a"));
}, \TypeError::class);
Assert::exception(function() use ($negFloat) {
	$negFloat->doMultiplication(new StringValue(" b"));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doMultiplication(new ArrayValue([]));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doMultiplication(new BoolValue(false));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doMultiplication(new RegexValue("/[abc]/"));
}, \TypeError::class);

//
// Test division.
//

Assert::exception(function() use ($float) {
	$float->doDivision(new NumberValue("-0"));
}, \Smuuf\Primi\ErrorException::class, '#Division.*zero#');
Assert::exception(function() use ($integer) {
	$integer->doDivision(new NumberValue(0));
}, \Smuuf\Primi\ErrorException::class, '#Division.*zero#');
Assert::same(-0.46, $float->doDivision(new NumberValue("-5"))->getInternalValue());
Assert::same(2.3, $float->doDivision(new NumberValue(1))->getInternalValue());
Assert::same(-1.15, $float->doDivision(new NumberValue(-2))->getInternalValue());

// Subtaction with unsupported formats will result in type error.
Assert::exception(function() use ($integer) {
	$integer->doDivision(new StringValue("1"));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doDivision(new ArrayValue([]));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doDivision(new BoolValue(false));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doDivision(new RegexValue("/[abc]/"));
}, \TypeError::class);

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
