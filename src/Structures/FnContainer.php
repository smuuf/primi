<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\CallFrame;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\ReturnException;
use \Smuuf\Primi\Ex\ArgumentCountError;
use \Smuuf\Primi\Scopes\Scope;
use \Smuuf\Primi\Scopes\AbstractScope;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Helpers\Stats;
use \Smuuf\StrictObject;
use \Smuuf\Primi\Helpers\Wrappers\ContextPushPopWrapper;
use \Smuuf\Primi\Handlers\HandlerFactory;

use \Smuuf\BetterExceptions\BetterException;
use \Smuuf\BetterExceptions\Types\ReturnTypeError;
use \Smuuf\BetterExceptions\Types\ArgumentTypeError;

/**
 * @internal
 */
class FnContainer {

	use StrictObject;

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
		string $definitionName,
		string $definitionModule,
		array $definitionArgs = [],
		?AbstractScope $definitionScope = \null
	) {

		$definitionArgsCount = \count($definitionArgs);

		// Prepare call name (will always be the same for all calls).
		$callName = "{$definitionName} in {$definitionModule}";

		// Invoking this closure is equal to standard execution of the nodes
		// that make up the body of the function.
		$closure = function(
			Context $ctx,
			array $args,
			?Location $callsite = null
		) use (
			$entryNode,
			$definitionScope,
			$definitionArgs,
			$definitionArgsCount,
			$callName
		) {

			// Check number of passed arguments.
			$args = \array_splice($args, 0, $definitionArgsCount);
			if ($definitionArgsCount > \count($args)) {
				throw new ArgumentCountError(
					\count($args),
					$definitionArgsCount
				);
			}

			// Create pairs of arguments <arg_name> => <arg_value> and
			// inject them into the function's scope, too. (i.e. these are
			// the arguments passed into it.)
			$args = \array_combine($definitionArgs, $args);

			$scope = new Scope;
			$scope->setParent($definitionScope);
			$scope->setVariables($args);

			$frame = new CallFrame($callName, $callsite);

			$wrapper = new ContextPushPopWrapper($ctx, $frame, $scope);
			return $wrapper->wrap(function($ctx) use ($entryNode) {

				// Run the function body and expect a ReturnException with the
				// return value.

				try {
					$handler = HandlerFactory::getFor($entryNode['name']);
					$handler::run($entryNode, $ctx);
				} catch (ReturnException $e) {
					return $e->getValue();
				}


			// Return null if no "return" was present (i.e. no
			// ReturnException was thrown from inside the called function).
			return Interned::null();
			});

		};

		return new self($closure, \false);

	}

	public static function buildFromClosure(callable $fn) {

		$closure = \Closure::fromCallable($fn);

		$rf = new \ReflectionFunction($closure);
		Func::check_allowed_parameter_types_of_function($rf);

		$callName = "{$rf->getName()} in <native>";
		$requiredArgumentCount = $rf->getNumberOfRequiredParameters();

		// Determine whether the runtime context object should be injected into
		// the function args (as the first one).
		$injectContext = false;
		$addToStack = true;
		if ($docComment = $rf->getDocComment()) {
			$injectContext = \strpos($docComment, '@injectContext') !== \false;
			$addToStack = \strpos($docComment, '@noStack') === \false;
		}

		$wrapper = function(
			Context $ctx,
			array $args,
			?Location $callsite = null
		) use (
			$closure,
			$requiredArgumentCount,
			$injectContext,
			$addToStack,
			$callName
		) {

			if ($injectContext) {
				\array_unshift($args, $ctx);
			}

			if ($requiredArgumentCount > \count($args)) {
				throw new ArgumentCountError(
					\count($args),
					$requiredArgumentCount
				);
			}

			// Add this function call to the call stack.
			// This is done manually without ContextPushPopWrapper for
			// performance reasons.
			if ($addToStack) {
				$frame = new CallFrame($callName, $callsite);
				$ctx->pushCall($frame);
			}

			try {
				$result = $closure(...$args);
			} catch (\TypeError $e) {

				$better = BetterException::from($e);

				// We want to handle only argument type errors. Return type
				// errors are a sign of badly used return type hint for PHP
				// function and should bubble up (be rethrown) for the
				// developer to see it.
				if ($better instanceof ReturnTypeError) {
					throw $e;
				}

				/** @var ArgumentTypeError $better */
				throw new TypeError(\sprintf(
					"Expected '%s' but got '%s' as argument %d",
					Func::php_types_to_primi_types($better->getExpected()),
					Func::php_types_to_primi_types($better->getActual()),
					$better->getArgumentIndex()
				));

			} finally {

				// Remove the latest entry from call stack.
				// This is done manually without ContextPushPopWrapper for
				// performance reasons.
				if ($addToStack) {
					$ctx->popCall();
				}

			}

			if (!$result instanceof AbstractValue) {
				return AbstractValue::buildAuto($result);
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

	public function callClosure(
		Context $ctx,
		array $args,
		?Location $callsite = null
	): ?AbstractValue {

		Stats::add('func_calls');
		return ($this->closure)($ctx, $args, $callsite);

	}

	public function isPhpFunction(): bool {
		return $this->isPhpFunction;
	}

}
