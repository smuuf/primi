<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use Smuuf\Primi\Code\Bytecode;
use Smuuf\StrictObject;
use Smuuf\Primi\Scope;
use Smuuf\Primi\Context;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Values\ModuleValue;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\CallConventions\PhpCallConvention;
use Smuuf\Primi\Helpers\CallConventions\CallArgsCallConvention;

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
	 * @param array $entryNode
	 * @phpstan-param TypeDef_AstNode $entryNode
	 * @param ?array{names: array<string, string>, defaults: array<string, TypeDef_AstNode>} $defParams
	 * @return self
	 */
	public static function build(
		Bytecode $bytecode,
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
		) use (
			$bytecode,
			$defScope,
			$defParams,
			$definitionModule,
			$definitionName
		) {

			$scope = new Scope([], parent: $defScope);
			$frame = $ctx->buildFrame(
				name: $definitionName,
				bytecode: $bytecode,
				scope: $scope,
				module: $definitionModule,
			);

			if ($defParams) {
				$callArgs = $args ?? CallArgs::getEmpty();
				$scope->setVariables(
					Func::resolve_default_args(
						$callArgs->extract(
							$defParams['names'] ?? [],
							\array_keys($defParams['defaults'] ?? [])
						),
						$defParams['defaults'],
						$ctx,
					)
				);
			}

			return $ctx->runFrame($frame);

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

		$callConv = $flagCallConventionArgsObject
			? new CallArgsCallConvention($closure)
			: new PhpCallConvention($closure, $rf);

		$wrapper = function(Context $ctx, ?CallArgs $args) use ($callConv) {

			return $callConv->call(
				$args ?? CallArgs::getEmpty(),
				$ctx,
			);

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
	): ?AbstractValue {
		return ($this->closure)($ctx, $args);
	}

	public function isPhpFunction(): bool {
		return $this->isPhpFunction;
	}

	public function getName(): string {
		return $this->name;
	}

}
