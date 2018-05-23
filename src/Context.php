<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\StrictObject;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\LazyValue;

class Context extends StrictObject implements IContext {

	// use WatchLifecycle;

	const EMPTY_CONTAINER = [
		'variables' => [],
	];

	private $container = self::EMPTY_CONTAINER;

	private $self;

	public function __construct(Value $self = null) {
		$this->self = $self;
	}

	public function reset() {
		$this->container = self::EMPTY_CONTAINER;
	}

	// Variables.

	public function setVariable(string $name, Value $value) {

		if ($this->self) {
			if ($value instanceof FuncValue || $value instanceof LazyValue) {
				$value->bind($this->self);
			}
		}

		$this->container['variables'][$name] = $value;

	}

	/**
	 * Set multiple variables to the context using an array as parameter.
	 *
	 * @param array<string, Value> $pairs
	 */
	public function setVariables(array $pairs) {

		foreach ($pairs as $name => $value) {

			if (!$value instanceof Value) {
				$value = Value::buildAutomatic($value);
			}

			// If this context has a "self" parent value defined, bind it to
			// functions and lazy values that may come out from it.

			// We might do this in the self::getVariable method, too, but
			// I work under assumption that setting variables is done
			// less frequently than getting it (in bench_simple.primi it was
			// 40004x setting vs 50005x getting), so we may gain some
			// performance boost by putting it here.

			if ($this->self) {
				if ($value instanceof FuncValue || $value instanceof LazyValue) {
					$value->bind($this->self);
				}
			}

			$this->setVariable($name, $value);

		}

	}

	public function getVariable(string $name): Value {

		if (!isset($this->container['variables'][$name])) {
			throw new InternalUndefinedVariableException($name);
		}

		$value = $this->container['variables'][$name];

		// We also resolve lazy values at this point.
		if ($value instanceof LazyValue) {
			return $value->resolve();
		}

		return $value;

	}

	public function getVariables(): array {
		return $this->container['variables'];
	}

	// Debugging.

	public function ___debug_zvals() {

		if (extension_loaded('xdebug_debug_zval')) {
			$tmp = $this;
			xdebug_debug_zval('tmp');
		}

	}

}
