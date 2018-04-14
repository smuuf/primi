<?php

use \Tester\Assert;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\ExtensionHub;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\ErrorEception;
use \Smuuf\Primi\InternalUndefinedPropertyException;

require __DIR__ . '/../bootstrap.php';

function get_val(Value $v) {
	return $v->getInternalValue();
}

class BadExtension {

}

class CustomExtension extends Extension {

    public static function funnyreverse(StringValue $self, StringValue $prefix): StringValue {
        return new StringValue($prefix->value . '_' . strrev($self->value));
    }

    public static function first(StringValue $self, StringValue $argument): StringValue {
        return new StringValue("1st {$argument->value}");
    }

}

//
// Adding a not-an-extension.
//

// Test that trying to add a extension that doesn't really
// if an extension..
Assert::exception(function() {
    ExtensionHub::add(BadExtension::class, StringValue::class);
}, \LogicException::class, '#not a valid#i');

//
// adding proper extension.
//

$string = new StringValue("jelenovi pivo nelej");

// No extension added yet, expecting "undefined method" error.
Assert::exception(function() use ($string) {
    $string->call('funnyreverse', [new StringValue('haha')]);
}, InternalUndefinedPropertyException::class);

// Adding proper extension throws no error.
Assert::noError(function() {
    ExtensionHub::add(CustomExtension::class, StringValue::class);
});

// Strings created _after_ the extension is registered are aware of the
// registered extension.
$stringTwo = new StringValue("jelenovi pivo nelej");

// After the extension is added, the method is now available for the newly
// created string value.
$result = $stringTwo->call('funnyreverse', [new StringValue('haha')]);
Assert::same('haha_jelen ovip ivonelej', get_val($result));

// Test passing in wrong number of arguments. (we expect PHP internal exception here.)
Assert::exception(function() use ($stringTwo) {
    $stringTwo->call('funnyreverse');
}, \ArgumentCountError::class);

// Test that standard library's first() method was sucessfully overridden.
$result = $stringTwo->call('first', [new StringValue('kalamář')]);
Assert::same('1st kalamář', get_val($result));
