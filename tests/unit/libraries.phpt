<?php

use \Tester\Assert;

use \Smuuf\Primi\Library;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ErrorEception;
use \Smuuf\Primi\InternalUndefinedMethodException;

require __DIR__ . '/../bootstrap.php';

function get_val(Value $v) {
	return $v->getInternalValue();
}

abstract class BadLibrary {

}

abstract class CustomLibrary extends Library {

    public static function funnyreverse(StringValue $self, StringValue $prefix): StringValue {
        return new StringValue($prefix->value . '_' . strrev($self->value));
    }

    public static function first(StringValue $self, StringValue $argument): StringValue {
        return new StringValue("1st {$argument->value}");
    }

}

//
// Registering a not-a-library.
//

// Test that trying to register a library that does not extend the Library class throws a
Assert::exception(function() {
    StringValue::registerLibrary(BadLibrary::class);
}, \LogicException::class, '#Unable to register#i');

//
// Registering proper library.
//

$string = new StringValue("jelenovi pivo nelej");

// No library registered yet, expecting "undefined method" error.
Assert::exception(function() use ($string) {
    $string->call('funnyreverse', [new StringValue('haha')]);
}, InternalUndefinedMethodException::class);

// Registering proper library throws no error.
Assert::noError(function() {
    StringValue::registerLibrary(CustomLibrary::class);
});

// After the library is registered, the method is now available for the string value.
$result = $string->call('funnyreverse', [new StringValue('haha')]);
Assert::same('haha_jelen ovip ivonelej', get_val($result));

// Test passing in wrong number of arguments. (we expect PHP internal exception here.)
Assert::exception(function() use ($string) {
    $string->call('funnyreverse');
}, \ArgumentCountError::class);

// Test that standard library's first() method was sucessfully overridden.
$result = $string->call('first', [new StringValue('kalamář')]);
Assert::same('1st kalamář', get_val($result));
