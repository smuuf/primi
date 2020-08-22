<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Ex\ArgumentCountError;
use \Smuuf\Primi\Ex\ReturnException;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\Value;

class FnContainer extends \Smuuf\Primi\StrictObject {

	/** @var \Closure Closure wrapping the function itself. */
	protected $closure;

	/** @var int Number of parameters the function is aware of. */
	protected $argsCount = 0;

	/** @var bool Does this function originate from Primi or its provided by engine? */
	protected $isPhpFunction = \false;

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
				// Intentionally shallow clone.
				$context = clone $definitionContext;
			} else {
				$context = new Context;
			}

			// Chack number of passed arguments.
			$args = \array_splice($args, 0, \count($definitionArgs));
			if (\count($definitionArgs) > \count($args)) {
				throw new ArgumentCountError(
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

		return new self($closure, \false, \count($definitionArgs));

	}

	public static function buildFromClosure(callable $fn) {

		$closure = \Closure::fromCallable($fn);

		$rf = new \ReflectionFunction($closure);
		$paramCount = $rf->getNumberOfParameters();
		$expectedTypes = Func::get_primi_parameter_types_from_function($rf);

		$wrapper = function(Value ...$args) use ($closure, $expectedTypes) {

			$maxIndex = \count($expectedTypes) - 1;
			// Do our own type checking prior to the invocation.
			// If we were only detecting ordinary \TypeErrors by PHP, we
			// wouldn't be able to tell where exactly those type errors
			// occured (for example we wouldn't be able to differentiate
			// argument type errors from return value type errors).
			foreach ($args as $i => $arg) {

				// Handle variadic parameters - actual number of arguments
				// can be higher than the expected number of parameters.
				// In that case, the type of the last (variadic) parameter
				// must be the same for all remaining arguments.
				$expectedType = $expectedTypes[$i] ?? $expectedTypes[$maxIndex];
				Func::allow_argument_types($i, $arg, $expectedType);

			}

			try {
				$result = $closure(...$args);
			} catch (\ArgumentCountError $e) {
				[$passed, $expected] = Func::parse_argument_count_error($e);
				throw new ArgumentCountError($passed, $expected);
			}

			if (!$result instanceof Value) {
				return Value::buildAutomatic($result);
			}

			return $result;

		};

		return new self($wrapper, \true, $paramCount);

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
