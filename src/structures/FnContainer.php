<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;

use \Smuuf\Primi\ReturnException;
use \Smuuf\Primi\InternalArgumentCountException;

class FnContainer extends \Smuuf\Primi\StrictObject {

	/** @var \Closure Closure wrapping the function itself. **/
	protected $closure;

	/** @var array Array containing parameters the function is aware of. **/
	protected $argsCount = 0;

	/**
	 * Build and return a closure wrapper around a Primi function (represented
	 * by its node tree).
	 *
	 * The closure returns some Primi value object as a result.
	 */
	public static function build(
		array $node,
		array $definitionArgs = [],
		Context $definitionContext = \null
	) {

		// Invoking this closure is equal to standard execution of the nodes
		// that make up the body of the function.
		$closure = function($self = null, ...$args) use ($node, $definitionContext, $definitionArgs) {

			// Create new context (scope) for the function, so it doesn't
			// operate in the global scope.
			$context = new Context;

			// Inject variables from the context of the place of the function's
			// definition into the function.
			if ($definitionContext) {
				$context->setVariables($definitionContext->getVariables());
			}

			$args = \array_splice($args, 0, \count($definitionArgs));
			if (\count($definitionArgs) > \count($args)) {
				throw new InternalArgumentCountException(
					\count($args),
					\count($definitionArgs)
				);
			}

			// Create pairs of arguments <arg_name> => <arg_value> and
			// inject them into the function's context, too. (i.e. these are
			// the arguments passed into it.)
			$args = \array_combine($definitionArgs, $args);

			// If this function has "self" specified, pass it as the "this"
			// variable.
			if ($self) {
				$args['this'] = $self;
			}

			$context->setVariables($args);

			try {

				// Run the function body and expect a ReturnException with the
				// return value.
				$handler = HandlerFactory::get($node['name']);
				$handler::handle($node, $context);

			} catch (ReturnException $e) {
				return $e->getValue();
			}

			// Return null if no "return" was present.
			return new NullValue;

		};

		return new self($closure, count($definitionArgs));

	}

	public static function buildNative(callable $fn) {

		$closure = \Closure::fromCallable($fn);

		$r = new \ReflectionFunction($closure);
		$argsCount = $r->getNumberOfRequiredParameters();

		$wrapper = function($self = null, ...$args) use ($closure) {

			// If this function has "self" specified, pass it as the first
			// argument.
			if ($self) {
				array_unshift($args, $self);
			}

			return $closure(...$args);

		};

		return new self($wrapper, $argsCount);

	}

	public static function buildRaw(callable $fn) {

		$closure = \Closure::fromCallable($fn);

		$r = new \ReflectionFunction($closure);
		$argsCount = $r->getNumberOfRequiredParameters();

		// Wrap the closure into another closure which handles automatic
		// conversion of parameter types (Primi values -> PHP values) and
		// return value type (PHP value -> Primi value).
		$wrapper = function($self = null, ...$args) use ($closure) {

			// If this function has "self" specified, pass it as the first
			// argument.
			if ($self) {
				array_unshift($args, $self);
			}

			$args = \array_map(function(Value $value) {
				return $value->getInternalValue();
			}, $args);

			$result = $closure(...$args);

			if (!$result instanceof Value) {
				return Value::buildAutomatic($result);
			}

			return $result;

		};

		return new self($wrapper, $argsCount);

	}

	/**
	 * Disallow direct instantiation. Always use the static factories above.
	 */
	private function __construct(\Closure $closure, int $argsCount = 0) {
		$this->closure = $closure;
		$this->argsCount = $argsCount;
	}

	public function getClosure() {
		return $this->closure;
	}

	public function getArgsCount(): int {
		return $this->argsCount;
	}

}
