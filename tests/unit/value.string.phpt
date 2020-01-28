<?php

use \Smuuf\Primi\ExtensionHub;
use \Smuuf\Primi\Structures\{
	StringValue,
	NumberValue,
	RegexValue,
	ArrayValue,
	BoolValue,
	Value
};

use \Tester\Assert;

require __DIR__ . '/../bootstrap.php';

function get_val(Value $v) {
	return $v->getInternalValue();
}

$fns = ExtensionHub::get();
$string = new StringValue("this is a string.");
$letterA = new StringValue("a");
$unicode = new StringValue("ťhiš íš á ŠTřing.");
$withNewline = new StringValue('a \n b');
$withNewlineLiteral = new StringValue('a \\\n b');

//
// Test sequence expanding...
//

Assert::same(2, count(explode("\n", get_val($withNewline))));

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

// Subtracting wrong value type results in type error.
Assert::exception(function() use ($string) {
	$string->doSubtraction(new NumberValue(1));
}, \TypeError::class);
Assert::exception(function() use ($string) {
	$string->doSubtraction(new BoolValue(true));
}, \TypeError::class);
Assert::exception(function() use ($string) {
	$string->doSubtraction(new ArrayValue([]));
}, \TypeError::class);

// Adding wrong value type results in type error.
Assert::exception(function() use ($string) {
	$string->doAddition(new BoolValue(false));
}, \TypeError::class);
Assert::exception(function() use ($string) {
	$string->doAddition(new RegexValue("[abc]+"));
}, \TypeError::class);
Assert::exception(function() use ($string) {
	$string->doAddition(new ArrayValue([]));
}, \TypeError::class);

//
// Multiplication.
//

// Multiplication by an integer number.
$result = $string->doMultiplication(new NumberValue(2))->getInternalValue();
Assert::same("this is a string.this is a string.", $result);
$result = $unicode->doMultiplication(new NumberValue(3))->getInternalValue();
Assert::same("ťhiš íš á ŠTřing.ťhiš íš á ŠTřing.ťhiš íš á ŠTřing.", $result);

// Multiplication with float number will result in type error.
Assert::exception(function() use ($string) {
	$string->doMultiplication(new NumberValue(2.1));
}, \TypeError::class);
Assert::exception(function() use ($unicode) {
	$unicode->doMultiplication(new NumberValue("3.1459"));
}, \TypeError::class);

//
// Test comparison operators...
//

// Equality: Two different instances containing the same "string": True.
$tmp = $string->doComparison("==", new StringValue("this is a string."));
Assert::true(get_val($tmp));
// Inequality: Two different instances containing the same "string": False.
$tmp = $string->doComparison("!=", new StringValue("this is a string."));
Assert::false(get_val($tmp));
// Equality: Two different instances containing different "string": False.
$tmp = $string->doComparison("==", new StringValue("dayum"));
Assert::false(get_val($tmp));
// Inequality: Two different instances containing different string": True.
$tmp = $string->doComparison("!=", new StringValue("boii"));
Assert::true(get_val($tmp));

// Equality: Comparing string against a number. (is always false)
$tmp = $string->doComparison("==", new NumberValue(5));
Assert::false(get_val($tmp));
$tmp = (new StringValue("5"))->doComparison("==", new NumberValue(5));
Assert::false(get_val($tmp));
$tmp = (new StringValue("2.1"))->doComparison("==", new NumberValue(2.1));
Assert::false(get_val($tmp));
$tmp = (new StringValue("50"))->doComparison("==", new NumberValue(5));
Assert::false(get_val($tmp));

// Equality: This is weird, but probably valid (albeit pretty unexpected, maybe
// a TO DO for future?). Number 2.0 is casted to "2" and "2.0" == "2" is false.
$tmp = (new StringValue("2.0"))->doComparison("==", new NumberValue(2.0));
Assert::false(get_val($tmp));

// Equality: Comparing string against matching regex: True.
$tmp = $string->doComparison("==", new RegexValue("s[tr]+"));
Assert::true(get_val($tmp));
// Inequality: Comparing string against non-matching regex: False.
$tmp = $string->doComparison("!=", new RegexValue("\d+"));
Assert::true(get_val($tmp));
// Equality: Comparing Unicode string against matching regex: True.
$tmp = $unicode->doComparison("==", new RegexValue('Š[Tř]{2}i'));
Assert::true(get_val($tmp));
// Inquality: Comparing Unicode string against non-matching regex: True.
$tmp = $unicode->doComparison("!=", new RegexValue('nuancé'));
Assert::true(get_val($tmp));

Assert::exception(function() use ($string) {
	$string->doComparison("!=", new BoolValue(false));
}, \TypeError::class);
Assert::exception(function() use ($string) {
	$string->doComparison("==", new ArrayValue([]));
}, \TypeError::class);
// Test that completely bogus operator throws error.
Assert::exception(function() use ($string) {
	$string->doComparison("@==!", new StringValue("wtf"));
}, \TypeError::class);

//
// Test dereferencing and insertion...
//

// Dereferencing returns new instance.
$dereferenced1 = $string->arrayGet(0);
Assert::notSame($string, $dereferenced1);

// Test return values of dereferencing.
Assert::same("t", get_val($string->arrayGet(0)));
Assert::same("s", get_val($string->arrayGet(3)));

// Test error when dereferencing from undexined index.
Assert::exception(function() use ($string) {
	$string->arrayGet(50);
}, \Smuuf\Primi\InternalUndefinedIndexException::class);

// Test that inserting does happen on the same instance of the value object.
$copy = clone $string;
// Test classic insertion.
$copy->arraySet(0, new StringValue("x"));
Assert::same("xhis is a string.", get_val($copy));
$copy->arraySet(2, new StringValue("u"));
Assert::same("xhus is a string.", get_val($copy));
// Test insertion without specifying index - Single letter.
$copy->arraySet(null, new StringValue("A"));
Assert::same("xhus is a string.A", get_val($copy));
// Test insertion without specifying index - Multiple letters.
$copy->arraySet(null, new StringValue("BBB"));
Assert::same("xhus is a string.ABBB", get_val($copy));

// Test creating insertion proxy and commiting it.
$proxy = $copy->getArrayInsertionProxy(4);
$proxy->commit(new StringValue("O"));
Assert::same("xhusOis a string.ABBB", get_val($copy));

// Test iteration of strings.
$sourceString = "abc\ndef";
$iterable = new StringValue($sourceString);
foreach ($iterable->getIterator() as $index => $x) {
	Assert::same($sourceString[$index], get_val($x));
}

//
// Test methods...
//

// Test classic formatting


$template = new StringValue("1:{},2:{},3:{},4:{}");
$result = $fns['string_format']->invoke([
	$template,
	new StringValue("FIRST"),
	new StringValue("SECOND"),
	new StringValue("THIRD"),
	new StringValue("FOURTH"),
]);
Assert::same("1:FIRST,2:SECOND,3:THIRD,4:FOURTH", get_val($result));

// Test combining positional and non-positional placeholders - forbidden.
Assert::exception(function() use ($fns) {
	$template = new StringValue("1:{},2:{2},3:{1},4:{}");
	$fns['string_format']->invoke([
		$template,
		new StringValue("FIRST"),
		new StringValue("SECOND"),
		new StringValue("THIRD"),
		new StringValue("FOURTH"),
	]);
}, \Smuuf\Primi\ErrorException::class);

// Test too-few-parameters.
Assert::exception(function() use ($fns) {
	$template = new StringValue("1:{},2:{},3:{},4:{}");
	$fns['string_format']->invoke([
		$template,
		new StringValue("FIRST"),
		new StringValue("SECOND"),
	]);
}, \Smuuf\Primi\ErrorException::class);

// Test placeholder index being too high for passed parameters.
Assert::exception(function() use ($fns) {
	$template = new StringValue("1:{0},2:{1000}");
	$fns['string_format']->invoke([
		$template,
		new StringValue("FIRST"),
		new StringValue("SECOND"),
	]);
}, \Smuuf\Primi\ErrorException::class);

//
// Test count.
//

Assert::same(3, get_val($fns['string_number_of']->invoke([$string, new StringValue("i")])));
Assert::same(2, get_val($fns['string_number_of']->invoke([$string, new StringValue("is")])));
Assert::same(0, get_val($fns['string_number_of']->invoke([$string, new StringValue("xoxoxo")])));
Assert::same(0, get_val($fns['string_number_of']->invoke([$string, new NumberValue(1)])));

//
// Test shuffle.
//

Assert::same(17, mb_strlen(get_val($fns['string_shuffle']->invoke([$string]))));
Assert::same(17, mb_strlen(get_val($fns['string_shuffle']->invoke([$unicode]))));

//
// Test length.
//

Assert::same(17, get_val($fns['string_length']->invoke([$string])));
Assert::same(1, get_val($fns['string_length']->invoke([$letterA])));
// Multibyte strings should report length correctly.
Assert::same(17, get_val($fns['string_length']->invoke([$unicode])));
// "\n" is expanded as newline - that's one character.
Assert::same(5, $fns['string_length']->invoke([$withNewline])->getInternalValue());
// "\\n" should not be expanded as newline
Assert::same(6, $fns['string_length']->invoke([$withNewlineLiteral])->getInternalValue());

//
// Test replacing.
//

$fnReplace = $fns['string_replace'];

// Test replacing with array of needle-replacement.
$pairs = new ArrayValue([
	"is" => new StringValue("A"),
	"i" => new StringValue("B"),
	"." => new StringValue("ščř"),
]);
$result = $fnReplace->invoke([$string, $pairs]);
Assert::same("thA A a strBngščř", get_val($result));
// Replacing ordinary strings.
$result = $fnReplace->invoke([$string, new StringValue("is"), new StringValue("yes!")]);
Assert::same("thyes! yes! a string.", get_val($result));
// Replacing with regex needle.
$result = $fnReplace->invoke([$string, new RegexValue('(i?s|\s)'), new StringValue("no!")]);
Assert::same("thno!no!no!no!ano!no!tring.", get_val($result));

//
// Test first/last occurence search.
//

$fnFirst = $fns['string_find_first'];
$fnLast = $fns['string_find_last'];

Assert::same(2, get_val($fnFirst->invoke([$string, new StringValue("is")])));
Assert::same(5, get_val($fnLast->invoke([$string, new StringValue("is")])));

// First: False when it does not appear in the string.
Assert::false(get_val($fnFirst->invoke([$string, new StringValue("aaa")])));
// Last: False when it does not appear in the string.
Assert::false(get_val($fnLast->invoke([$string, new StringValue("aaa")])));

//
// Test splitting.
//

$string = new StringValue("hello,how,are,you");
$result = [];
foreach (get_val($fns['string_split']->invoke([$string, new StringValue(",")])) as $item) {
	$result[] = get_val($item);
}
Assert::same(["hello", "how", "are", "you"], $result);

$string = new StringValue("well, this ... IS ... awkward!");
$result = [];
foreach (get_val($fns['string_split']->invoke([$string, new RegexValue("[,\s\.]+")])) as $item) {
	$result[] = get_val($item);
}
Assert::same(["well", "this", "IS", "awkward!"], $result);

//
// Test reverse.
//

$fnReverse = $fns['string_reverse'];

// Simple ascii string.
$string = new StringValue("You wake me up, god damn it!");
Assert::same("!ti nmad dog ,pu em ekaw uoY", get_val($fnReverse->invoke([$string])));

// With accents
$string = new StringValue("Čauky mňauky, kolovrátku ;D");
Assert::same("D; uktárvolok ,ykuaňm ykuaČ", get_val($fnReverse->invoke([$string])));

// With the worst smiley ever.
$string = new StringValue("Yoo 🤣, my mæte 😂!!!");
Assert::same("!!!😂 etæm ym ,🤣 ooY", get_val($fnReverse->invoke([$string])));
