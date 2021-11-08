<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\StackFrame;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\ReturnException;
use \Smuuf\Primi\Ex\ArgumentCountError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Handlers\HandlerFactory;

use \Smuuf\BetterExceptions\BetterException;
use \Smuuf\BetterExceptions\Types\ReturnTypeError;
use \Smuuf\BetterExceptions\Types\ArgumentTypeError;

use \Smuuf\Primi\Values\ModuleValue;

/**
 * @internal
 */
class FnContainer {

	public const FLAG_INJECT_CONTEXT = 1;
	public const FLAG_NO_STACK = 2;

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
		ModuleValue $definitionModule,
		array $definitionArgs = [],
		?Scope $definitionScope = \null
	) {

		$definitionArgsCount = \count($definitionArgs);

		// Invoking this closure is equal to standard execution of the nodes
		// that make up the body of the function.
		$closure = function(
			Context $ctx,
			array $args,
			?Location $callsite = \null
		) use (
			$entryNode,
			$definitionScope,
			$definitionArgs,
			$definitionArgsCount,
			$definitionModule,
			$definitionName
		) {

			if (count($args) < $definitionArgsCount) {
				throw new ArgumentCountError(
					count($args),
					$definitionArgsCount
				);
			}

			// Create dict array with function arguments in form of
			// [<arg_name> => <arg_value>].
			$finalArgs = [];

			if ($args) {
				$argIdx = 0;
				while ($argIdx < $definitionArgsCount) {
					$finalArgs[$definitionArgs[$argIdx]] = $args[$argIdx];
					$argIdx++;
				}
			}

			$scope = new Scope;
			$scope->setVariables($finalArgs);

			if ($definitionScope !== \null) {
				$scope->setParent($definitionScope);
			}

			$frame = new StackFrame(
				$definitionName,
				$definitionModule,
				$callsite
			);

			// For performance reasons (function calls are frequent) push and
			// pop stack frame and scope manually, without the overhead
			// of using ContextPushPopWrapper.
			$ctx->pushCall($frame);
			$ctx->pushScope($scope);

			try {
				HandlerFactory::runNode($entryNode, $ctx);
			} catch (ReturnException $e) {

				// This is the return value of the function call.
				return $e->getValue();

			} finally {
				$ctx->popScope();
				$ctx->popCall();
			}

			// Return null if no "return" was present (i.e. no
			// ReturnException was thrown from inside the called function).
			return Interned::null();

		};

		return new self($closure, \false);

	}

	public static function buildFromClosure(callable $fn, array $flags = []) {

		$closure = \Closure::fromCallable($fn);

		$rf = new \ReflectionFunction($closure);
		Func::check_allowed_parameter_types_of_function($rf);

		$callName = "{$rf->getName()} in <native>";
		$requiredArgumentCount = $rf->getNumberOfRequiredParameters();

		$flagInjectContext = in_array(self::FLAG_INJECT_CONTEXT, $flags);
		$flagToStack = !in_array(self::FLAG_NO_STACK, $flags);

		$wrapper = function(
			Context $ctx,
			array $args,
			?Location $callsite = \null
		) use (
			$closure,
			$requiredArgumentCount,
			$callName,
			$flagInjectContext,
			$flagToStack
		) {

			if ($flagInjectContext) {
				\array_unshift($args, $ctx);
			}

			if ($requiredArgumentCount > \count($args)) {
				// If context was supposed to be injected, that should be
				// a transparent, behind-the-scene thing, and we should not
				// count the "context" argument into the number of arguments.
				throw new ArgumentCountError(
					\count($args) - ($flagInjectContext ? 1 : 0),
					$requiredArgumentCount - ($flagInjectContext ? 1 : 0)
				);
			}

			// Add this function call to the call stack. This is done manually
			// without ContextPushPopWrapper for performance reasons.
			if ($flagToStack) {

				$frame = new StackFrame(
					$callName,
					$ctx->getCurrentModule(),
					$callsite
				);

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

				$argIndex = $better->getArgumentIndex();
				throw new TypeError(\sprintf(
					"Expected '%s' but got '%s' as argument %d",
					implode('|', $better->getExpected()),
					$args[$argIndex - 1]->getTypeName(),
					$argIndex
				));

			} finally {

				// Remove the latest entry from call stack.
				// This is done manually without ContextPushPopWrapper for
				// performance reasons.
				if ($flagToStack) {
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
		?Location $callsite = \null
	): ?AbstractValue {
		return ($this->closure)($ctx, $args, $callsite);
	}

	public function isPhpFunction(): bool {
		return $this->isPhpFunction;
	}

}
