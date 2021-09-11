<?php

use \Tester\Assert;

use \Smuuf\Primi\Ex\IndexError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\{
	StringValue,
	NumberValue,
	RegexValue,
	NullValue,
	DictValue,
	ListValue,
	AbstractValue,
};
use \Smuuf\Primi\Helpers\Interned;

require __DIR__ . '/../bootstrap.php';

function get_val(AbstractValue $v) {
	return $v->getInternalValue();
}

$string = new StringValue("this is a string.");
$letterA = new StringValue("a");
$unicode = new StringValue("ťhiš íš á ŠTřing.");

//
// Escaping sequences are NOT handled inside StringValue, but instead during the
// handling of source code string literal.
//

$stringsWithEscapeSequences = [
	'a\nb',
	'a \n b',
	'a \\n b',
	'a\\n\nb',
	'a\\\n\t\\tb',
];
foreach ($stringsWithEscapeSequences as $s) {
	Assert::same($s, get_val(new StringValue($s)));
}

// Get correct repr - things should be quoted and escaped properly.
// REMEMBER: Escape characters are NOT HANDLED when creating StringValue
// objects. Whatever is put into StringValue as argument will literally be
// what's inside.
Assert::same('"\""', (new StringValue('"'))->getStringRepr());
Assert::same('"\\\\\""', (new StringValue('\\"'))->getStringRepr());
Assert::same('"\\\\\'"', (new StringValue("\'"))->getStringRepr());
Assert::same('"\\\\\\\\\'"', (new StringValue("\\\\'"))->getStringRepr());
Assert::same('"\\\\n"', (new StringValue('\n'))->getStringRepr());
Assert::same('"\\n"', (new StringValue("\n"))->getStringRepr());
// Correct value - NOT repr. Should be the same as input.
Assert::same('"', (new StringValue('"'))->getStringValue());
Assert::same('\\"', (new StringValue('\\"'))->getStringValue());
Assert::same("\'", (new StringValue("\'"))->getStringValue());
Assert::same("\\\\'", (new StringValue("\\\\'"))->getStringValue());
Assert::same('\n', (new StringValue('\n'))->getStringValue());
Assert::same("\n",(new StringValue("\n"))->getStringValue());

//
// Test adding and subtracting...
//

// Concatenate two strings (the same string).
Assert::same(
	"this is a string.this is a string.",
	get_val($string->doAddition($string))
);

// Subtracting string from string (letter "a").
$noA = $string->doSubtraction($letterA);
Assert::same(
	"this is  string.",
	get_val($noA)
);

// Subtract regex matching all whitespace.
$regexWhitespace = new RegexValue("\s+");
$noSpaces = $noA->doSubtraction($regexWhitespace);
Assert::same(
	"thisisstring.",
	get_val($noSpaces)
);

// Subtraction with undefined results will return ordinary null.
Assert::null($string->doSubtraction(new NumberValue(1)));
Assert::null($string->doSubtraction(Interned::bool(true)));
Assert::null($string->doSubtraction(new DictValue([])));

// Addition with undefined results will return ordinary null.
Assert::null($string->doAddition(Interned::bool(false)));
Assert::null($string->doAddition(new RegexValue("[abc]+")));
Assert::null($string->doAddition(new DictValue([])));

//
// Multiplication.
//

// Multiplication by an integer number.
$result = $string->doMultiplication(new NumberValue(2))->getInternalValue();
Assert::same("this is a string.this is a string.", $result);
$result = $unicode->doMultiplication(new NumberValue(3))->getInternalValue();
Assert::same("ťhiš íš á ŠTřing.ťhiš íš á ŠTřing.ťhiš íš á ŠTřing.", $result);

// Multiplication with expected type but with invalid value will throw error.
Assert::exception(function() use ($string) {
	$string->doMultiplication(new NumberValue(2.1));
}, RuntimeError::class);
Assert::exception(function() use ($unicode) {
	$unicode->doMultiplication(new NumberValue("3.1459"));
}, RuntimeError::class);

//
// Test comparison operators...
//

// Equality: Two different instances containing the same "string": True.
Assert::true($string->isEqualTo(new StringValue("this is a string.")));
// Inequality: Two different instances containing the same "string": False.
Assert::false(!$string->isEqualTo(new StringValue("this is a string.")));
// Equality: Two different instances containing different "string": False.
Assert::false($string->isEqualTo(new StringValue("dayum")));
// Inequality: Two different instances containing different string": True.
Assert::true(!$string->isEqualTo(new StringValue("boii")));

// Equality: Comparing string against matching regex.
Assert::true($string->isEqualTo(new RegexValue("s[tr]+")));
Assert::true(!$string->isEqualTo(new RegexValue("\d+")));
Assert::true($unicode->isEqualTo(new RegexValue('Š[Tř]{2}i')));
Assert::true(!$unicode->isEqualTo(new RegexValue('nuancé')));

// Equality with numbers: Strings do not know about numbers, so all
// these are null (i.e. a string value doesn't know how to resolve equality
// with number values.)
Assert::null($string->isEqualTo(new NumberValue(5)));
Assert::null((new StringValue("5"))->isEqualTo(new NumberValue(5)));
Assert::null((new StringValue("2.1"))->isEqualTo(new NumberValue(2.1)));
Assert::null((new StringValue("50"))->isEqualTo(new NumberValue(5)));
Assert::null((new StringValue("2.0"))->isEqualTo(new NumberValue(2.0)));

Assert::null($string->isEqualTo(Interned::bool(false)));
Assert::null($string->isEqualTo(new NullValue()));
Assert::null($string->isEqualTo(new ListValue([])));
Assert::null($string->isEqualTo(new DictValue([])));

//
// Test dereferencing and insertion...
//

// Dereferencing returns new instance.
$dereferenced1 = $string->itemGet(new NumberValue(0));
Assert::notSame($string, $dereferenced1);

// Test return values of dereferencing.
Assert::same("t", get_val($string->itemGet(new NumberValue(0))));
Assert::same("s", get_val($string->itemGet(new NumberValue(3))));

// Test error when dereferencing from undexined index.
Assert::exception(function() use ($string) {
	$string->itemGet(new NumberValue(50));
}, IndexError::class);

// Test that inserting does happen on the same instance of the value object.
$copy = clone $string;

// Test classic insertion - which is forbidden for strings, as it would be
// unclear (for user) if it is mutated in the process or not.
Assert::false($copy->itemSet(new NumberValue(0), new StringValue("x")));

// Test iteration of strings.
$sourceString = "abc\ndef";
$iterable = new StringValue($sourceString);
foreach ($iterable->getIterator() as $index => $x) {
	Assert::same($sourceString[get_val($index)], get_val($x));
}
