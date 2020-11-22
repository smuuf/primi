<?php

use \Tester\Assert;

use \Smuuf\Primi\Context;
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

	/**
	 * @injectContext
	 */
	public function get_var_from_context(Context $ctx, StringValue $varName): Value {
		return $ctx->getVariable($varName->value);
	}

}

// Create custom extension hub.
$extHub = new ExtensionHub;

//
// Trying to add a not-an-extension.
//

Assert::exception(function() use ($extHub) {
	$extHub->add(BadExtension::class);
}, EngineError::class, '#not a valid#i');

//
// Stuff from extensions should be available after resetting main context.
//

$i = new \Smuuf\Primi\Interpreter(null, null, $extHub);
$s = $i->getCurrentScope();
Assert::falsey($s->getVariables(), 'User-lang variable pool is empty.');
Assert::truthy($s->getVariables(true), 'Global variables were loaded from extension and thus the context is not completely empty.');

//
// Cannot add new extensions to hub after it was applied to some context.
//

Assert::exception(function() use ($extHub) {
	$extHub->add(CustomExtension::class);
}, EngineError::class, '#.*hub.*locked#i');

//
// Create a new interpreter and extension hub.
//

// Create custom extension hub.
$extHub = new ExtensionHub;
$extHub->add(CustomExtension::class);
$i = new \Smuuf\Primi\Interpreter(null, null, $extHub);
$s = $i->getCurrentScope();

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
Assert::same( '4', $s->getVariable('c')->getInternalValue(), 'Internal representation of number is normalized upon instantiation of the number object.');
Assert::same('4', $s->getVariable('c')->getStringValue(), 'String representation of number is normalized - extra zeroes are trimmed');

$src = <<<SRC
xxx = get_var_from_context('c')
SRC;

$i->run($src);
Assert::same('4', $s->getVariable('xxx')->getStringValue(), "Variable 'xxx' is filled by function accessing the interpreter context.");
