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

	public function funnyreverse(StringValue $self, StringValue $prefix): StringValue {
		return new StringValue($prefix->value . '_' . strrev($self->value));
	}

	public function first(StringValue $self, StringValue $argument): StringValue {
		return new StringValue("1st {$argument->value}");
	}

}

//
// Adding a not-an-extension.
//

// Test that trying to add a extension that doesn't really
// if an extension..
Assert::exception(function() {
	ExtensionHub::add(BadExtension::class);
}, \LogicException::class, '#not a valid#i');
