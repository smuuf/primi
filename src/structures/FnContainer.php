<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Statistics;
use \Smuuf\Primi\AbstractScope;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\ReturnException;
use \Smuuf\Primi\Ex\ArgumentCountError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\Value;

/**
 * @internal
 */
class FnContainer extends \Smuuf\Primi\StrictObject {

	/** @var \Closure Closure wrapping the function itself. */
	protected $closure;

	/**
	 * True if the function represents a native PHP function instead of Primi
	 * function.
	 * @var bool
	 */
	protected $isPhpFunction = \false;

	/**
	 * Build and return a closure wrapper around a Primi function (represented
	 * by its node tree).
	 *
	 * The closure returns some Primi value object as a result.
	 */
	public static function build(
		array $entryNode,
		array $definitionArgs = [],
		?AbstractScope $definitionScope = \null,
		?string $callId = \null
	) {

		$callId = $callId ?? '<unknown>';

		// Invoking this closure is equal to standard execution of the nodes
		// that make up the body of the function.
		$closure = function(Context $ctx, array $args) use (
			$entryNode,
			$definitionScope,
			$definitionArgs,
			$callId
		) {

			// Check number of passed arguments.
			$args = \array_splice($args, 0, \count($definitionArgs));
			if (\count($definitionArgs) > \count($args)) {
				throw new ArgumentCountError(
					\count($args),
					\count($definitionArgs)
				);
			}

			// Create pairs of arguments <arg_name> => <arg_value> and
			// inject them into the function's scope, too. (i.e. these are
			// the arguments passed into it.)
			$args = \array_combine($definitionArgs, $args);

			$scope = new Scope;
			$scope->setParent($definitionScope);
			$scope->setVariables($args);

			$ctx->pushScope($scope);
			$ctx->pushCall($callId);

			try {

				// Run the function body and expect a ReturnException with the
				// return value.
				$handler = HandlerFactory::get($entryNode['name']);
				$handler::run($entryNode, $ctx);

			} catch (ReturnException $e) {
				return $e->getValue();
			} finally {

				// Remove the latest context stack items.
				$ctx->popScope();
				$ctx->popCall();

			}

			// Return null if no "return" was present.
			return new NullValue;

		};

		return new self($closure, \false);

	}

	public static function buildFromClosure(callable $fn) {

		$closure = \Closure::fromCallable($fn);

		$rf = new \ReflectionFunction($closure);
		$callId = $rf->getName();

		// Determine whether context object should be injected into args.
		$docComment = $rf->getDocComment();
		$injectContext = \strpos($docComment, '@injectContext') !== \false;

		Func::check_allowed_parameter_types_of_function($rf);

		$wrapper = function(Context $ctx, array $args) use (
			$closure,
			$injectContext,
			$callId
		) {

			if ($injectContext) {
				\array_unshift($args, $ctx);
			}

			// Add this function call to the call stack.
			$ctx->pushCall($callId);

			try {
				$result = $closure(...$args);
			} catch (\ArgumentCountError $e) {

				[$passed, $expected] = Func::parse_argument_count_error($e);
				throw new ArgumentCountError($passed, $expected);

			} catch (\TypeError $e) {

				// We want to handle only argument type errors. Return type
				// errors are a sign of badly used return type hint for PHP
				// function and should bubble up (be rethrown) for the
				// developer to see it.
				if (\strpos($e->getMessage(), 'TypeError: Return') !== \false) {
					throw $e;
				}

				[$index, $passed, $expected] = Func::parse_argument_type_error($e);

				throw new TypeError(\sprintf(
					"Expected '%s' but got '%s' as argument %d",
					$expected,
					$passed,
					$index
				));

			} finally {
				$ctx->popCall(); // Remove the latest entry from call stack.
			}

			if (!$result instanceof Value) {
				return Value::buildAutomatic($result);
			}

			return $result;

		};

		return new self($wrapper, \true);

	}

	/**
	 * Disallow direct instantiation. Always use the static factories above.
	 */
	private function __construct(
		\Closure $closure,
		bool $isPhpFunction
	) {
		$this->closure = $closure;
		$this->isPhpFunction = $isPhpFunction;
	}

	public function callClosure(...$args): ?Value {
		return ($this->closure)(...$args);
	}

	public function isPhpFunction(): bool {
		return $this->isPhpFunction;
	}

}
