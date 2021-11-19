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
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\DictValue;
use \Smuuf\Primi\Values\TupleValue;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Helpers\CallConventions\PhpCallConvention;
use \Smuuf\Primi\Helpers\CallConventions\ArgsObjectCallConvention;
use \Smuuf\Primi\Handlers\HandlerFactory;

/**
 * @internal
 */
class FnContainer {

	public const FLAG_INJECT_CONTEXT = 1;
	public const FLAG_NO_STACK = 2;
	public const FLAG_CALLCONVENTION_ARGSOBJECT = 3;

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
		array $defParams = [],
		?Scope $defScope = \null
	) {

		// Invoking this closure is equal to standard execution of the nodes
		// that make up the body of the function.
		$closure = function(
			Context $ctx,
			?CallArgs $args = \null,
			?Location $callsite = \null
		) use (
			$entryNode,
			$defScope,
			$defParams,
			$definitionModule,
			$definitionName
		) {

			$scope = new Scope;
			if ($defScope !== \null) {
				$scope->setParent($defScope);
			}

			if ($args) {
				self::prepareArgs($defParams, $args, $scope);
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
		$callName = "{$rf->getName()} in <native>";

		$flagInjectContext = in_array(self::FLAG_INJECT_CONTEXT, $flags, \true);
		$flagToStack = !in_array(self::FLAG_NO_STACK, $flags, \true);
		$flagCallConventionArgsObject =
			in_array(self::FLAG_CALLCONVENTION_ARGSOBJECT, $flags, \true);

		$callConvention = $flagCallConventionArgsObject
			? new ArgsObjectCallConvention($closure)
			: new PhpCallConvention($closure, $rf);

		$wrapper = function(
			Context $ctx,
			?CallArgs $args,
			?Location $callsite = \null
		) use (
			$callConvention,
			$callName,
			$flagInjectContext,
			$flagToStack
		) {

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

				$result = $callConvention->call(
					$args ?? CallArgs::getEmpty(),
					$flagInjectContext ? $ctx : \null
				);

			} finally {

				// Remove the latest entry from call stack.
				// This is done manually without ContextPushPopWrapper for
				// performance reasons.
				if ($flagToStack) {
					$ctx->popCall();
				}

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
		?CallArgs $args = \null,
		?Location $callsite = \null
	): ?AbstractValue {
		return ($this->closure)($ctx, $args, $callsite);
	}

	public function isPhpFunction(): bool {
		return $this->isPhpFunction;
	}

	// Helpers.

	/**
	 * NOTE: Only docblock type-hinting for performance reasons.
	 *
	 * @param array $defParams
	 * @param CallArgs $callArgs
	 * @param Scope $scope
	 */
	private static function prepareArgs(
		$defParams,
		$callArgs,
		$scope
	): void {

		// Determine - if there are such things specified:
		// 1) Which argument will collect remaining positional arguments.
		// Such argument's name is prefixed with "*".
		// 2) Which argument will collect remaining keyword arguments.
		// Such argument's name is prefixed with "**".
		$posArgsCollector = \false;
		$kwArgsCollector = \false;
		foreach ($defParams as $defArgName => $_) {
			if ($defArgName[0] === '*') {

				if ($defArgName[1] === '*') {
					$kwArgsCollector = \substr($defArgName, 2);
				} else {
					$posArgsCollector = \substr($defArgName, 1);
				}

				// Remove these asterisk-prefixed parameters from definition
				// parameters, so they're not strictly expected.
				// (We check if all strictly-expected parameters have assigned
				// arguments at the end).
				unset($defParams[$defArgName]);

			}
		}

		// Final args dict will be based on the definition args dict,
		// which has the form ['arg_a': null, 'arg_b': null, ...], to which
		// we will add actual Primi objects to replace the nulls.
		// If there are any nulls remaining after preparing the args, we know
		// some were omitted by the caller.
		$finalArgs = $defParams;

		// If there is a keyword args collector, prepare a dict value for it and
		// place it into the final args dict array.
		// The called function still expects a dictionary, if "**kwargs" were
		// defined in the function parameters, even if it will be empty.
		if ($kwArgsCollector) {
			$kwArgsCollectorDict = new DictValue;
			$finalArgs[$kwArgsCollector] = $kwArgsCollectorDict;
		}

		// Process positional arguments first. array_slice() is used so - while
		// iterating over definition args dict is easier for us here - we don't
		// want to process more positional args than how many there actually
		// were passed by the caller.
		$i = -1;
		$args = $callArgs->getArgs();
		foreach (
			\array_slice($defParams, 0, \count($args)) as $defArgName => $_
		) {

			// For each definition arg that has a corresponding positional arg,
			// replace the "null" in the final args list with the actual
			// passed value from the positional args list.
			$finalArgs[$defArgName] = $args[++$i];

		}

		// Decide what to do with any remaining positional arguments.
		$remainingPosArgs = \array_slice($args, $i + 1);
		if ($remainingPosArgs) {
			if ($posArgsCollector) {
				$finalArgs[$posArgsCollector] = new TupleValue($remainingPosArgs);
			} else {
				$count = \count($remainingPosArgs);
				throw new TypeError(
					"Passed $count unexpected positional arguments");
			}
		}

		// If there is a positional args collector, but no positional arguments
		// remained, the function still expects an empty list as the "*args".
		if ($posArgsCollector && !isset($finalArgs[$posArgsCollector])) {
			$finalArgs[$posArgsCollector] = new TupleValue([]);
		}

		// Now let's process keyword arguments. At this point it's easier for us
		// to iterate over passed kwargs (instead of determining what is still
		// "left unprocessed" by the previous positional-args-processing).
		$callKwargs = $callArgs->getKwargs();
		foreach ($callKwargs as $key => $value) {

			// If this kwarg key is not at all present in known definition
			// args, we don't expect this kwarg, so we throw an error.
			if (!\array_key_exists($key, $defParams)) {
				if ($kwArgsCollector) {
					// If there was an unexpected kwarg, but there is a kwarg
					// collector defined, add this unexpected kwarg to it.
					$kwArgsCollectorDict->itemSet(Interned::string($key), $value);
					continue;
				} else {
					throw new TypeError("Unexpected keyword argument '$key'");
				}
			}

			// If this kwarg overwrites already specified final arg (unspecified
			// are false, so isset() works here), throw an error.
			if (!empty($finalArgs[$key])) {
				throw new TypeError("Argument '$key' passed multiple times");
			}

			$finalArgs[$key] = $value;

		}

		// If there are any "null" values left in the final args dict,
		// some arguments were left out and that is an error.
		$missingKey = \array_search(\false, $finalArgs, \true);
		if ($missingKey !== \false) {
			throw new TypeError("Missing required argument '$missingKey'");
		}

		$scope->setVariables($finalArgs);

	}

}
