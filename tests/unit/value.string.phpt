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

$string = new StringValue("this is a string.");
$letterA = new StringValue("a");
$unicode = new StringValue("ťhiš íš á ŠTřing.");
$withNewline = new StringValue('a \n b');

// Test sequence expanding...
Assert::same(2, count(explode("\n", $withNewline->getPhpValue())));

// Test adding and subtracting...

// Concatenate two strings (the same string).
Assert::same(
    "this is a string.this is a string.",
    $string->doAddition($string)->getPhpValue()
);

// Subtracting string from string (letter "a").
$noA = $string->doSubtraction($letterA);
Assert::same(
    "this is  string.",
    $noA->getPhpValue()
);

// Subtract regex matching all whitespace.
$regexWhitespace = new RegexValue("/\s+/");
$noSpaces = $noA->doSubtraction($regexWhitespace);
Assert::same(
    "thisisstring.",
    $noSpaces->getPhpValue()
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
    $string->doAddition(new RegexValue("/[abc]+/"));
}, \TypeError::class);
Assert::exception(function() use ($string) {
    $string->doAddition(new ArrayValue([]));
}, \TypeError::class);

// Test comparison operators...

function extract_bool_value(BoolValue $b) {
    return $b->getPhpValue();
}

// Equality: Two different instances containing the same "string": True.
$tmp = $string->doComparison("==", new StringValue("this is a string."));
Assert::true(extract_bool_value($tmp));
// Inequality: Two different instances containing the same "string": False.
$tmp = $string->doComparison("!=", new StringValue("this is a string."));
Assert::false(extract_bool_value($tmp));
// Equality: Two different instances containing different "string": False.
$tmp = $string->doComparison("==", new StringValue("dayum"));
Assert::false(extract_bool_value($tmp));
// Inequality: Two different instances containing different string": True.
$tmp = $string->doComparison("!=", new StringValue("boii"));
Assert::true(extract_bool_value($tmp));

// Equality: Comparing string against matching regex: True.
$tmp = $string->doComparison("==", new RegexValue("/s[tr]+/"));
Assert::true(extract_bool_value($tmp));
// Inequality: Comparing string against non-matching regex: False.
$tmp = $string->doComparison("!=", new RegexValue("/\d+/"));
Assert::true(extract_bool_value($tmp));
// Equality: Comparing Unicode string against matching regex: True.
$tmp = $unicode->doComparison("==", new RegexValue('/Š[Tř]{2}i/'));
Assert::true(extract_bool_value($tmp));
// Inquality: Comparing Unicode string against non-matching regex: True.
$tmp = $unicode->doComparison("!=", new RegexValue('/nuancé/'));
Assert::true(extract_bool_value($tmp));

// In/Equality: Comparing against unsupported value type.
Assert::exception(function() use ($string) {
    $string->doComparison("==", new NumberValue(5));
}, \TypeError::class);
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

// Test dereferencing and insertion...

// Dereferencing returns new instance.
$dereferenced1 = $string->dereference(new NumberValue(0));
Assert::notSame($string, $dereferenced1);

// Test return values of dereferencing.
Assert::same("t", $string->dereference(new NumberValue(0))->getPhpValue());
Assert::same("s", $string->dereference(new NumberValue(3))->getPhpValue());

// Test error when dereferencing from undexined index.
Assert::exception(function() use ($string) {
    $string->dereference(new NumberValue(50));
}, \Smuuf\Primi\InternalUndefinedIndexException::class);

// Test that inserting does happen on the same instance of the value object.
$copy = clone $string;
Assert::same($copy, $copy->insert(0, new StringValue("x")));
// Test classic insertion.
$copy->insert(2, new StringValue("u"));
Assert::same("xhus is a string.", $copy->getPhpValue());
// Test insertion without specifying index - Single letter.
$copy->insert("", new StringValue("A"));
Assert::same("xhus is a string.A", $copy->getPhpValue());
// Test insertion without specifying index - Multiple letters.
$copy->insert("", new StringValue("BBB"));
Assert::same("xhus is a string.ABBB", $copy->getPhpValue());

// Test creating insertion proxy and commiting it.
$proxy = $copy->getInsertionProxy(4);
$proxy->commit(new StringValue("O"));
Assert::same("xhusOis a string.ABBB", $copy->getPhpValue());

// Test iteration of strings.
$sourceString = "abc\ndef";
$iterable = new StringValue($sourceString);
foreach ($iterable->getIterator() as $index => $x) {
    Assert::same($sourceString[$index], $x->getPhpValue());
}

// Test methods...

// Test classic formatting
$template = new StringValue("1:{},2:{},3:{},4:{}");
$result = $template->callFormat(
    new StringValue("FIRST"),
    new StringValue("SECOND"),
    new StringValue("THIRD"),
    new StringValue("FOURTH")
);
Assert::same("1:FIRST,2:SECOND,3:THIRD,4:FOURTH", $result->getPhpValue());

// Test formatting with positions.
$template = new StringValue("1:{},2:{2},3:{1},4:{}");
$result = $template->callFormat(
    new StringValue("FIRST"),
    new StringValue("SECOND"),
    new StringValue("THIRD"),
    new StringValue("FOURTH")
);
Assert::same("1:FIRST,2:SECOND,3:FIRST,4:SECOND", $result->getPhpValue());

// Test too-few-parameters.
Assert::exception(function() {
    $template = new StringValue("1:{},2:{},3:{},4:{}");
    $result = $template->callFormat(
        new StringValue("FIRST"),
        new StringValue("SECOND")
    );
}, \Smuuf\Primi\ErrorException::class);

// Test too-few-parameters with positions.
Assert::exception(function() {
    $template = new StringValue("1:{},2:{1},3:{1},4:{}");
    $result = $template->callFormat(
        new StringValue("FIRST")
    );
}, \Smuuf\Primi\ErrorException::class);

// Test placeholder index being too high for passed parameters.
Assert::exception(function() {
    $template = new StringValue("1:{},2:{1000}");
    $result = $template->callFormat(
        new StringValue("FIRST"),
        new StringValue("SECOND")
    );
}, \Smuuf\Primi\ErrorException::class);

// Test count.
Assert::same(3, $string->callCount(new StringValue("i"))->getPhpValue());
Assert::same(2, $string->callCount(new StringValue("is"))->getPhpValue());
Assert::same(0, $string->callCount(new StringValue("xoxoxo"))->getPhpValue());
Assert::same(0, $string->callCount(new NumberValue(1))->getPhpValue());

// Test length.
Assert::same(17, $string->propLength()->getPhpValue());
Assert::same(1, $letterA->propLength()->getPhpValue());
// Multibyte strings should report length correctly.
Assert::same(17, $unicode->propLength()->getPhpValue());
// "\n" is expanded as newline - that's one character.
Assert::same(5, $withNewline->propLength()->getPhpValue());

// Test replacing.

// Test replacing with array of needle-replacement.
$pairs = new ArrayValue([
    "is" => new StringValue("A"),
    "i" => new StringValue("B"),
    "." => new StringValue("ščř"),
]);
$result = $string->callReplace($pairs);
Assert::same("thA A a strBngščř", $result->getPhpValue());
// Replacing ordinary strings.
$result = $string->callReplace(new StringValue("is"), new StringValue("yes!"));
Assert::same("thyes! yes! a string.", $result->getPhpValue());
// Replacing with regex needle.
$result = $string->callReplace(new RegexValue('(i?s|\s)'), new StringValue("no!"));
Assert::same("thno!no!no!no!ano!no!tring.", $result->getPhpValue());

// Test first/last occurence search.
Assert::same(2, $string->callFirst(new StringValue("is"))->getPhpValue());
Assert::same(5, $string->callLast(new StringValue("is"))->getPhpValue());

// First: False when it does not appear in the string.
Assert::false($string->callFirst(new StringValue("aaa"))->getPhpValue());
// Last: False when it does not appear in the string.
Assert::false($string->callLast(new StringValue("aaa"))->getPhpValue());


