<?php

use \Tester\Assert;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\ExtensionHub;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\Value;

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

// Test that trying to add a extension that doesn't really if an extension..
Assert::exception(function() {
	ExtensionHub::add(BadExtension::class);
}, \LogicException::class, '#not a valid#i');

//
// Stuff from extensions should be available after resetting main context.
//

$i = new \Smuuf\Primi\Interpreter;
$c = $i->getContext();
Assert::falsey($c->getVariables());
ExtensionHub::add(CustomExtension::class);
Assert::falsey($c->getVariables());

$i2 = new \Smuuf\Primi\Interpreter;
$c2 = $i2->getContext();
Assert::truthy($c2->getVariable('funnyreverse'));
$c2->reset();
Assert::truthy($c2->getVariable('funnyreverse'));
