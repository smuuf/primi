<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;

use \Smuuf\Primi\ReturnException;
use \Smuuf\Primi\InternalArgumentCountException;

class FnContainer extends \Smuuf\Primi\StrictObject {

	/** @var \Closure Closure wrapping the function itself. */
	protected $closure;

	/** @var int Number of parameters the function is aware of. */
	protected $argsCount = 0;

	/** @var bool Does this function originate from Primi or its provided by engine? */
	protected $isPhpFunction = 0;

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
		$closure = function(...$args) use (
			$node,
			$definitionContext,
			$definitionArgs
		) {

			// If there's a parent/definition context, clone a new context from
			// it, so the function does not mutate the outer scope.
			if ($definitionContext) {
				$context = clone $definitionContext;
			} else {
				$context = new Context;
			}

			// Chack number of passed arguments.
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

		return new self($closure, false, \count($definitionArgs));

	}

	public static function buildFromClosure(callable $fn) {

		$closure = \Closure::fromCallable($fn);

		$r = new \ReflectionFunction($closure);
		$argsCount = $r->getNumberOfRequiredParameters();

		// If the callable does not have a return type of Value, we will
		// handle consider the function as handling PHP values instead of
		// Primi value objects.
		$passPhpValues = \true;
		if (
			$r->hasReturnType()
			&& $r->getReturnType() instanceof \ReflectionNamedType
			&& \is_a($r->getReturnType()->getName(), Value::class, \true)
		) {
			$passPhpValues = \false;
		}

		$wrapper = function(...$args) use ($closure, $passPhpValues) {

			if ($passPhpValues) {
				$args = \array_map(function(Value $value) {
					return $value->getInternalValue();
				}, $args);
			}

			$result = $closure(...$args);

			if ($passPhpValues && !$result instanceof Value) {
				return Value::buildAutomatic($result);
			}

			return $result;

		};

		return new self($wrapper, true, $argsCount);

	}

	/**
	 * Disallow direct instantiation. Always use the static factories above.
	 */
	private function __construct(
		\Closure $closure,
		bool $isPhpFunction,
		int $argsCount
	) {
		$this->closure = $closure;
		$this->argsCount = $argsCount;
		$this->isPhpFunction = $isPhpFunction;
	}

	public function getClosure(): \Closure {
		return $this->closure;
	}

	public function getArgsCount(): int {
		return $this->argsCount;
	}

	public function isPhpFunction(): bool {
		return $this->isPhpFunction;
	}

}
