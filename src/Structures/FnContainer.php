<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Scope;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Location;
use \Smuuf\Primi\StackFrame;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Helpers\CallConventions\PhpCallConvention;
use \Smuuf\Primi\Helpers\CallConventions\CallArgsCallConvention;
use \Smuuf\Primi\Handlers\HandlerFactory;

/**
 * @internal
 */
class FnContainer {

	public const FLAG_TO_STACK = 1;
	public const FLAG_CALLCONV_CALLARGS = 2;

	use StrictObject;

	/** Closure wrapping the function itself. */
	private \Closure $closure;

	/** Function name. */
	private string $name;

	/**
	 * True if the function represents a native PHP function instead of Primi
	 * function.
	 */
	private bool $isPhpFunction = \false;

	/**
	 * Build and return a closure wrapper around a Primi function (represented
	 * by its node tree).
	 *
	 * The closure returns some Primi value object as a result.
	 *
	 * @param TypeDef_AstNode $entryNode
	 * @param ?array{names: array<string, string>, defaults: array<string, TypeDef_AstNode>} $defParams
	 * @return self
	 */
	public static function build(
		array $entryNode,
		string $definitionName,
		ModuleValue $definitionModule,
		?array $defParams = \null,
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

			$scope = new Scope([], parent: $defScope);

			$frame = new StackFrame(
				$definitionName,
				$definitionModule,
				$callsite
			);

			try {

				// Push call first for more precise traceback for errors when
				// resolving arguments (expected args may be missing, for
				// example) below.
				// For performance reasons (function calls are frequent) push
				// and pop stack frame and scope manually, without the overhead
				// of using ContextPushPopWrapper.
				$ctx->pushCallScopePair($frame, $scope);

				if ($defParams) {
					$callArgs = $args ?? CallArgs::getEmpty();
					$scope->setVariables(
						Func::resolve_default_args(
							$callArgs->extract(
								$defParams['names'],
								\array_keys($defParams['defaults'])
							),
							$defParams['defaults'],
							$ctx
						)
					);
				}

				HandlerFactory::runNode($entryNode, $ctx);

				// This is the return value of the function call.
				if ($ctx->hasRetval()) {
					return $ctx->popRetval()->getValue();
				}

				// Return null if no "return" was present in the called
				// function.
				return Interned::null();

			} finally {
				$ctx->popCallScopePair();
			}



		};

		return new self($closure, \false, $definitionName);

	}

	/**
	 * @param array<int> $flags
	 * @return self
	 */
	public static function buildFromClosure(callable $fn, array $flags = []) {

		$closure = \Closure::fromCallable($fn);

		$rf = new \ReflectionFunction($closure);
		$callName = $rf->getName();

		$flagToStack = \in_array(self::FLAG_TO_STACK, $flags, \true);
		$flagCallConventionArgsObject =
			\in_array(self::FLAG_CALLCONV_CALLARGS, $flags, \true);

		$callConvention = $flagCallConventionArgsObject
			? new CallArgsCallConvention($closure)
			: new PhpCallConvention($closure, $rf);

		$wrapper = function(
			Context $ctx,
			?CallArgs $args,
			?Location $callsite = \null
		) use (
			$callConvention,
			$callName,
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
					$ctx
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

		return new self($wrapper, \true, $callName);

	}

	/**
	 * Disallow direct instantiation. Always use the static factories above.
	 */
	private function __construct(
		\Closure $closure,
		bool $isPhpFunction,
		string $name
	) {
		$this->closure = $closure;
		$this->isPhpFunction = $isPhpFunction;
		$this->name = $name;
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

	public function getName(): string {
		return $this->name;
	}

}
