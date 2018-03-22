<?php

use \Tester\Assert;
use \Smuuf\Primi\Structures\{
	StringValue,
	NumberValue,
	RegexValue,
	ArrayValue,
	BoolValue
};

require __DIR__ . '/../bootstrap.php';

$integer = new NumberValue("1");
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

// Test that various input correcly decide which is int and which is float.
Assert::type('int', $integer->getInternalValue());
Assert::type('int', $posInteger->getInternalValue());
Assert::type('int', $negInteger->getInternalValue());
Assert::type('float', $integeryFloat->getInternalValue());
Assert::type('float', $posIntegeryFloat->getInternalValue());
Assert::type('float', $negIntegeryFloat->getInternalValue());
Assert::type('float', $float->getInternalValue());
Assert::type('float', $posFloat->getInternalValue());
Assert::type('float', $negFloat->getInternalValue());
Assert::type('int', $zero->getInternalValue());
Assert::type('int', $posZero->getInternalValue());
Assert::type('int', $negZero->getInternalValue());
Assert::type('float', $posZeroFloat->getInternalValue());
Assert::type('float', $negZeroFloat->getInternalValue());

// Test correct detection of "numeric" string.
Assert::true(NumberValue::isNumeric("1"));
Assert::true(NumberValue::isNumeric("1.2"));
Assert::true(NumberValue::isNumeric("0.0"));
Assert::true(NumberValue::isNumeric("-0.0"));
Assert::true(NumberValue::isNumeric("-1.2"));
Assert::true(NumberValue::isNumeric("-1"));
Assert::false(NumberValue::isNumeric("hell no"));
Assert::false(NumberValue::isNumeric("not 1"));
Assert::false(NumberValue::isNumeric("1 owl"));
Assert::false(NumberValue::isNumeric("2 2"));
Assert::true(NumberValue::isNumeric("+0.0"));
Assert::false(NumberValue::isNumeric("+-1"));

// Test addition.

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

// String values that are numeric will be added like numbers.
Assert::same(1, $integer->doAddition(new StringValue("-0"))->getInternalValue());
Assert::same(-4, $integer->doAddition(new StringValue("-5"))->getInternalValue());
Assert::same(2.2, $integer->doAddition(new StringValue("+1.2"))->getInternalValue());
Assert::same(-1.2, $integer->doAddition(new StringValue("-2.2"))->getInternalValue());

// String values that are not numeric will result in concatenation instead of addition.
$word1 = $integer->doAddition(new StringValue("a word"));
$word2 = $integer->doAddition(new StringValue("-1 owls"));
Assert::same("1a word", $word1->getInternalValue());
Assert::same("1-1 owls", $word2->getInternalValue());
// The result of number+string is a string value.
Assert::type(StringValue::class, $word1);
Assert::type(StringValue::class, $word2);

// Addition with unsupported formats will result in type error.
Assert::exception(function() use ($integer) {
	$integer->doAddition(new ArrayValue([]));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doAddition(new BoolValue(true));
}, \TypeError::class);
Assert::exception(function() use ($integer) {
	$integer->doAddition(new RegexValue("/[abc]/"));
}, \TypeError::class);

// Test subtraction.

Assert::same(1, $integer->doSubtraction(new NumberValue("-0"))->getInternalValue());
Assert::same(6, $integer->doSubtraction(new NumberValue("-5"))->getInternalValue());
Assert::same(1, $integer->doSubtraction(new NumberValue(0))->getInternalValue());
Assert::same(0, $integer->doSubtraction(new NumberValue(1))->getInternalValue());
Assert::same(124, $integer->doSubtraction(new NumberValue(-123))->getInternalValue());

// Subtaction with unsupported formats will result in type error.
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

// Test multiplication.

Assert::same(0, $float->doMultiplication(new NumberValue("-0"))->getInternalValue());
Assert::same(-11.5, $float->doMultiplication(new NumberValue("-5"))->getInternalValue());
Assert::same(0, $float->doMultiplication(new NumberValue(0))->getInternalValue());
Assert::same(2.3, $float->doMultiplication(new NumberValue(1))->getInternalValue());
Assert::same(-282.9, $float->doMultiplication(new NumberValue(-123))->getInternalValue());

// Subtaction with unsupported formats will result in type error.
Assert::exception(function() use ($integer) {
	$integer->doMultiplication(new StringValue("1"));
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

// Test division.

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

// Test unary.

// Unary addition returns new value.
Assert::same(2, $integer->doUnary("++")->getInternalValue());
// Unary subtract returns new value.
Assert::same(0, $integer->doUnary("--")->getInternalValue());
// Some bogust unary operator throws error.
Assert::exception(function() use ($integer) {
	$integer->doUnary("!@=");
}, \TypeError::class);

// Test comparison operators...

function extract_bool_value(BoolValue $b) {
	return $b->getInternalValue();
}

$tmp = $integer->doComparison("==", new NumberValue("-1"));
Assert::false(extract_bool_value($tmp));

$tmp = $integer->doComparison("!=", new NumberValue("-1"));
Assert::true(extract_bool_value($tmp));

$tmp = $integer->doComparison("==", new NumberValue("1"));
Assert::true(extract_bool_value($tmp));

$tmp = $integer->doComparison("==", new NumberValue("1.0"));
Assert::true(extract_bool_value($tmp));

$tmp = $integer->doComparison("!=", new NumberValue("2"));
Assert::true(extract_bool_value($tmp));

$tmp = $float->doComparison(">", new NumberValue("2"));
Assert::true(extract_bool_value($tmp));

$tmp = $float->doComparison("<", new NumberValue("2.3"));
Assert::false(extract_bool_value($tmp));

$tmp = $float->doComparison(">=", new NumberValue("2.31"));
Assert::false(extract_bool_value($tmp));

$tmp = $float->doComparison("<=", new NumberValue("2.31"));
Assert::true(extract_bool_value($tmp));

//
// Methods-
//

$tmp = $integer->call('sqrt')->getInternalValue();
Assert::type('int', $tmp);
$tmp = $integer->call('pow', [new NumberValue(4)])->getInternalValue();
Assert::type('int', $tmp);
$tmp = $integer->call('sin')->getInternalValue();
Assert::type('float', $tmp);
$tmp = $integer->call('cos')->getInternalValue();
Assert::type('float', $tmp);
$tmp = $integer->call('tan')->getInternalValue();
Assert::type('float', $tmp);
$tmp = $integer->call('atan')->getInternalValue();
Assert::type('float', $tmp);
$tmp = $integer->call('ceil')->getInternalValue();
Assert::type('int', $tmp);
$tmp = $integer->call('floor')->getInternalValue();
Assert::type('int', $tmp);
$tmp = $integer->call('round')->getInternalValue();
Assert::type('int', $tmp);

$tmp = $float->call('sqrt')->getInternalValue();
Assert::type('float', $tmp);
$tmp = $float->call('pow', [new NumberValue(4)])->getInternalValue();
Assert::type('float', $tmp);
$tmp = $float->call('sin')->getInternalValue();
Assert::type('float', $tmp);
$tmp = $float->call('cos')->getInternalValue();
Assert::type('float', $tmp);
$tmp = $float->call('tan')->getInternalValue();
Assert::type('float', $tmp);
$tmp = $float->call('atan')->getInternalValue();
Assert::type('float', $tmp);
$tmp = $float->call('ceil')->getInternalValue();
Assert::type('int', $tmp);
$tmp = $float->call('floor')->getInternalValue();
Assert::type('int', $tmp);
$tmp = $float->call('round')->getInternalValue();
Assert::type('int', $tmp);
