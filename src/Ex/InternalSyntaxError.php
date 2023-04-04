<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

/**
 * Internal syntax error exception thrown during parsing/compilation of some
 * piece of Primi source code.
 *
 * @internal
 */
class InternalSyntaxError extends EngineException {

	/**
	 * @param int Byte offset in the source code where the error occurred.
	 * @param null|string $reason Specific reason of the syntax error,
	 *     if specified.
	 */
	public function __construct(
		public readonly int $offset,
		public readonly ?string $reason = \null,
	) {}

	/**
	 * @param array $node AST node where the syntax error originates.
	 * @param null|string $reason Specific reason of the syntax error,
	 *     if specified.
	 */
	public static function fromNode(
		array $node,
		?string $reason = null,
	): self {
		return new self($node['offset'], $reason);
	}

}
