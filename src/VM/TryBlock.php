<?php

declare(strict_types=1);

namespace Smuuf\Primi\VM;

use Smuuf\StrictObject;
use Smuuf\Primi\ScopeComposite;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Helpers\Types;
use Smuuf\Primi\Handlers\Kinds\Variable;

/**
 * Data structure for representing try-catch blocks which are to be stored
 * inside a Frame object.
 *
 * @see Frame
 * @internal
 */
class TryBlock {

	use StrictObject;

	/**
	 * @param list<array{string, int}> $spec List of couples specifying
	 *     type of exception-to-be-caught and the opcode index in current
	 *     frame's opcodes where to jump if we catch it.
	 * @param int $originalStackSize Remembering number of values on value stack
	 *     when entering the try-catch block. When the exception is caught, the
	 *     value stack must be restored to this length (this ensures that
	 *     no extra intermediate values are kept on the value stack when an
	 *     exception is caught even mid-opcode execution).
	 */
	public function __construct(
		private readonly array $spec,
		public readonly int $originalStackSize,
	) {}

	/**
	 * @param array<array{string, int, ?string}> $pairs List of tuples
	 *     specifying type of exception-to-be-caught and the opcode index in
	 *     current frame's opcodes where to jump if we catch it.
	 * @param int $stackSize Remembering number of values on value stack
	 *     when entering the try-catch block. When the exception is caught, the
	 *     value stack must be restored to this length (this ensures that
	 *     no extra intermediate values are kept on the value stack when an
	 *     exception is encountered even mid-opcode execution).
	 */
	public static function fromPairs(
		array $pairs,
		int $originalStackSize,
		ScopeComposite $scopeComp,
	): self {

		$spec = [];
		foreach ($pairs as $pair) {

			// Convert ['SomeVariableName', 123] into [<some object>, 123].
			// The last pair will have null (no exception type specified - the
			// fallback "catch") - and we support that, too.
			$spec[] = [
				$pair[0]
					? Variable::fetch($pair[0], $scopeComp)
					: null,
				$pair[1], // Target op index.
				$pair[2] ?? null, // "as" variable name.
			];
		}

		return new self($spec, $originalStackSize);

	}

	/**
	 * @return null|array{int, ?string} Null, if the exception is not caught
	 *     by any of the catches, or OP index to jump if it is caught + optional
	 *     a variable name into which to store caught exception object.
	 */
	public function resolveJumpIndex(AbstractValue $exception): ?array {

		foreach ($this->spec as [$expectedType, $index, $varName]) {
			if (
				$expectedType === null // General "catch" or ...
				|| Types::isInstanceOf($exception, $expectedType) // Specific exception type.
			) {
				return [$index, $varName];
			}
		}

		return null;

	}

}
