<?php

use \Tester\Assert;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\ExtensionHub;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\BreakException;
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

	public static function raise_break(): void {
		throw new BreakException;
	}

}

//
// Adding a not-an-extension.
//

// Test that trying to add a extension that doesn't really if an extension..
Assert::exception(function() {
	ExtensionHub::add(BadExtension::class);
}, EngineError::class, '#not a valid#i');

//
// Stuff from extensions should be available after resetting main context.
//

$i = new \Smuuf\Primi\Interpreter;
$c = $i->getContext();
Assert::falsey($c->getVariables());
Assert::truthy($c->getVariables(true), 'Default extensions are loaded at this point - those provide globals and thus thecontext is not completely empty');

// This has no effect on existing context - extensions are applied only on new
// contexts initialized by new interpreter instance.
ExtensionHub::add(CustomExtension::class);

$i = new \Smuuf\Primi\Interpreter;
$c = $i->getContext();
Assert::truthy($c->getVariable('funnyreverse'));
$c->reset();
Assert::truthy($c->getVariable('funnyreverse'), 'Global functions still exist after resetting the context without the $wipeGlobals argument.');

$src = <<<SRC
c = 0
while (c < 10) {
	if (c > 3) {
		raise_break()
	}
	c = c + 1
}
SRC;

$i->run($src);
Assert::same(4, $c->getVariable('c')->getInternalValue());
