<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\ScopeComposite;
use Smuuf\Primi\VM\Machine;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Handlers\Handler;
use Smuuf\Primi\Compiler\Compiler;

class Variable extends Handler {

	public static function fetch(
		string $name,
		ScopeComposite $scopeComp,
	): AbstractValue {

		$value = $scopeComp->getVariable($name);

		if ($value === \null) {
			Exceptions::piggyback(
				StaticExceptionTypes::getNameErrorType(),
				"Undefined variable '$name'",
			);
		}

		return $value;

	}

	public static function reduce(array &$node): void {
		$node['var'] = $node['core']['text'];
		unset($node['core']);
	}

	public static function compile(Compiler $bc, array $node): void {
		$bc->add(Machine::OP_LOAD_NAME, $node['var']);
	}

}
