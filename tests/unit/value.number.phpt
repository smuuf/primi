<?php

use \Tester\Assert;
use \Smuuf\Primi\Structures\{
    StringValue,
    NumberValue,
    RegexValue,
    ArrayValue,
    BoolValue
};

require __DIR__ . '/bootstrap.php';

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
Assert::type('int', $integer->getPhpValue());
Assert::type('int', $posInteger->getPhpValue());
Assert::type('int', $negInteger->getPhpValue());
Assert::type('float', $integeryFloat->getPhpValue());
Assert::type('float', $posIntegeryFloat->getPhpValue());
Assert::type('float', $negIntegeryFloat->getPhpValue());
Assert::type('float', $float->getPhpValue());
Assert::type('float', $posFloat->getPhpValue());
Assert::type('float', $negFloat->getPhpValue());
Assert::type('int', $zero->getPhpValue());
Assert::type('int', $posZero->getPhpValue());
Assert::type('int', $negZero->getPhpValue());
Assert::type('float', $posZeroFloat->getPhpValue());
Assert::type('float', $negZeroFloat->getPhpValue());

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
Assert::same(1, $integer->doAddition(new NumberValue("-0"))->getPhpValue());
// Addition with a negative 5 constructed from string.
Assert::same(-4, $integer->doAddition(new NumberValue("-5"))->getPhpValue());
// Addition with a proper zero Number value.
Assert::same(1, $integer->doAddition(new NumberValue(0))->getPhpValue());
// Addition with a proper Number one.
Assert::same(2, $integer->doAddition(new NumberValue(1))->getPhpValue());
// Addition with a proper negative Number.
Assert::same(-122, $integer->doAddition(new NumberValue(-123))->getPhpValue());

// String values that are numeric will be added like numbers.
Assert::same(1, $integer->doAddition(new StringValue("-0"))->getPhpValue());
Assert::same(-4, $integer->doAddition(new StringValue("-5"))->getPhpValue());
Assert::same(2.2, $integer->doAddition(new StringValue("+1.2"))->getPhpValue());
Assert::same(-1.2, $integer->doAddition(new StringValue("-2.2"))->getPhpValue());

// String values that are not numeric will result in concatenation instead of addition.
$word1 = $integer->doAddition(new StringValue("a word"));
$word2 = $integer->doAddition(new StringValue("-1 owls"));
Assert::same("1a word", $word1->getPhpValue());
Assert::same("1-1 owls", $word2->getPhpValue());
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

Assert::same(1, $integer->doSubtraction(new NumberValue("-0"))->getPhpValue());
Assert::same(6, $integer->doSubtraction(new NumberValue("-5"))->getPhpValue());
Assert::same(1, $integer->doSubtraction(new NumberValue(0))->getPhpValue());
Assert::same(0, $integer->doSubtraction(new NumberValue(1))->getPhpValue());
Assert::same(124, $integer->doSubtraction(new NumberValue(-123))->getPhpValue());

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

Assert::same(0, $float->doMultiplication(new NumberValue("-0"))->getPhpValue());
Assert::same(-11.5, $float->doMultiplication(new NumberValue("-5"))->getPhpValue());
Assert::same(0, $float->doMultiplication(new NumberValue(0))->getPhpValue());
Assert::same(2.3, $float->doMultiplication(new NumberValue(1))->getPhpValue());
Assert::same(-282.9, $float->doMultiplication(new NumberValue(-123))->getPhpValue());

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
}, \ErrorException::class, '#Division.*zero#');
Assert::exception(function() use ($integer) {
    $integer->doDivision(new NumberValue(0));
}, \ErrorException::class, '#Division.*zero#');
Assert::same(-0.46, $float->doDivision(new NumberValue("-5"))->getPhpValue());
Assert::same(2.3, $float->doDivision(new NumberValue(1))->getPhpValue());
Assert::same(-1.15, $float->doDivision(new NumberValue(-2))->getPhpValue());

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
Assert::same(2, $integer->doUnary("++")->getPhpValue());
// Unary subtract returns new value.
Assert::same(0, $integer->doUnary("--")->getPhpValue());
// Some bogust unary operator throws error.
Assert::exception(function() use ($integer) {
    $integer->doUnary("!@=");
}, \TypeError::class);

// Test comparison operators...

function extract_bool_value(BoolValue $b) {
    return $b->getPhpValue();
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
