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
use \Smuuf\Primi\Helpers\Func;

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

			$scope = new Scope;
			if ($defScope !== \null) {
				$scope->setParent($defScope);
			}

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

			} catch (ReturnException $e) {

				// This is the return value of the function call.
				return $e->getValue();

			} finally {
				$ctx->popCallScopePair();
			}

			// Return null if no "return" was present (i.e. no
			// ReturnException was thrown from inside the called function).
			return Interned::null();

		};

		return new self($closure, \false);

	}

	/**
	 * @param array<int> $flags
	 * @return self
	 */
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

}
