<?php

use Tester\Assert;

use Smuuf\Primi\Values\{
	AbstractValue,
	StringValue,
	NumberValue,
	RegexValue,
	DictValue,
	ListValue
};
use Smuuf\Primi\Helpers\Interned;

require __DIR__ . '/../bootstrap.php';

function get_val(AbstractValue $v) {
	return $v->getCoreValue();
}

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
// All numbers are actually stored as strings.
//

Assert::type('string', get_val($integer));
Assert::type('string', get_val($posInteger));
Assert::type('string', get_val($negInteger));
Assert::type('string', get_val($integeryFloat));
Assert::type('string', get_val($posIntegeryFloat));
Assert::type('string', get_val($negIntegeryFloat));
Assert::type('string', get_val($float));
Assert::type('string', get_val($posFloat));
Assert::type('string', get_val($negFloat));
Assert::type('string', get_val($zero));
Assert::type('string', get_val($posZero));
Assert::type('string', get_val($negZero));
Assert::type('string', get_val($posZeroFloat));
Assert::type('string', get_val($negZeroFloat));

//
// Test addition.
//

// Addition with a negative 0 constructed from string.
Assert::same(
	"1",
	get_val($integer->doAddition(new NumberValue("-0")))
);
// Addition with a negative 5 constructed from string.
Assert::same(
	"-4",
	get_val($integer->doAddition(new NumberValue("-5")))
);
// Addition with a proper zero Number value.
Assert::same(
	"1",
	get_val($integer->doAddition(new NumberValue(0)))
);
// Addition with a proper Number one.
Assert::same(
	"2",
	get_val($integer->doAddition(new NumberValue(1)))
);
// Addition with a proper negative Number.
Assert::same(
	"-122",
	get_val($integer->doAddition(new NumberValue(-123)))
);

// Addition with unsupported formats will result in null - unhandled case.
Assert::null($integer->doAddition(new StringValue("4")));
Assert::null($integer->doAddition(new DictValue([])));
Assert::null($integer->doAddition(Interned::bool(true)));
Assert::null($integer->doAddition(new RegexValue("/[abc]/")));

//
// Test subtraction.
//

Assert::same(
	'1',
	get_val($integer->doSubtraction(new NumberValue("-0")))
);
Assert::same(
	'6',
	get_val($integer->doSubtraction(new NumberValue("-5")))
);
Assert::same(
	'1',
	get_val($integer->doSubtraction(new NumberValue(0)))
);
Assert::same(
	'0',
	get_val($integer->doSubtraction(new NumberValue(1)))
);
Assert::same(
	'124',
	get_val($integer->doSubtraction(new NumberValue(-123)))
);

// Subtraction with unsupported formats will result in null - unhandled case.
Assert::null($integer->doSubtraction(new StringValue("1")));
Assert::null($integer->doSubtraction(new DictValue([])));
Assert::null($integer->doSubtraction(Interned::bool(false)));
Assert::null($integer->doSubtraction(new RegexValue("/[abc]/")));

//
// Test multiplication.
//

// Multiplication with numbers.

Assert::same(
	'0',
	get_val($float->doMultiplication(new NumberValue("-0")))
);
Assert::same(
	'-11.5',
	get_val($float->doMultiplication(new NumberValue("-5")))
);
Assert::same(
	'0',
	get_val($float->doMultiplication(new NumberValue(0)))
);
Assert::same(
	"2.3",
	get_val($float->doMultiplication(new NumberValue(1)))
);
Assert::same(
	"-282.9",
	get_val($float->doMultiplication(new NumberValue(-123)))
);

// Multiplication by a string.
// Number * String is not supported by Number, but
// String * Number is supported.
$string = new StringValue(" _ěšč");
$result = get_val($biggerInteger->doMultiplication($string));
Assert::same(" _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč", $result);
$result = get_val($string->doMultiplication($biggerInteger));
Assert::same(" _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč _ěšč", $result);

// Multiplication with unsupported formats will result in null - unhandled case.
//Assert::null($integer->doMultiplication(new StringValue(" b")));

assert_piggyback_exception(
	fn() => $posFloat->doMultiplication(new StringValue(" a")),
	'TypeError',
	'#String multiplier must be a positive integer#i'
);

assert_piggyback_exception(
	fn() => $negFloat->doMultiplication(new StringValue(" b")),
	'TypeError',
	'#String multiplier must be a positive integer#i'
);

Assert::null($integer->doMultiplication(new DictValue([])));
Assert::null($integer->doMultiplication(Interned::bool(false)));
Assert::null($integer->doMultiplication(new RegexValue("/[abc]/")));

//
// Power.
//

Assert::same('1', get_val($biggerInteger->doPower(new NumberValue('0'))));
Assert::same('20', get_val($biggerInteger->doPower(new NumberValue('1'))));
Assert::same('400', get_val($biggerInteger->doPower(new NumberValue('2'))));
Assert::same('0.0025', get_val($biggerInteger->doPower(new NumberValue('-2'))));
Assert::same('104857600000000000000000000', get_val($biggerInteger->doPower($biggerInteger)));

Assert::same('1', get_val($posFloat->doPower(new NumberValue('0'))));
Assert::same('2.3', get_val($posFloat->doPower(new NumberValue('1'))));
Assert::same('5.29', get_val($posFloat->doPower(new NumberValue('2'))));
Assert::same(
	'0.18903591682419659735349716446124763705103969754253308128544423440453686200378071833648393194706994328922495274102079395085066162',
	get_val($posFloat->doPower(new NumberValue('-2')))
);

assert_piggyback_exception(
	fn() => $posFloat->doPower(new NumberValue('4.1')),
	'TypeError',
	'#Exponent must be integer#i',
);

assert_piggyback_exception(
	fn() => $posFloat->doPower(new NumberValue('-4.1')),
	'TypeError',
	'#Exponent must be integer#i',
);

//
// Test division.
//

Assert::same(
	"-0.46",
	get_val($float->doDivision(new NumberValue("-5")))
);
Assert::same(
	"2.3",
	get_val($float->doDivision(new NumberValue(1)))
);
Assert::same(
	"-1.15",
	get_val($float->doDivision(new NumberValue(-2)))
);

assert_piggyback_exception(
	fn() => $float->doDivision(new NumberValue("-0")),
	'RuntimeError',
	'#Division.*zero#i',
);

assert_piggyback_exception(
	fn() => $integer->doDivision(new NumberValue(0)),
	'RuntimeError',
	'#Division.*zero#i',
);

// Subtraction with unsupported formats will result in null - unhandled case.
Assert::null($integer->doDivision(new StringValue("1")));
Assert::null($integer->doDivision(new ListValue([])));
Assert::null($integer->doDivision(Interned::bool(false)));
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
