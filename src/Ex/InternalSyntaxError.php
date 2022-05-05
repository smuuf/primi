<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

/**
 * Internal syntax error exception thrown by the parser, which operates on a
 * raw string, without any knowledge about a specific source code or
 * module/file.
 *
 * This internal syntax error should be caught and converted to a proper syntax
 * error represented by the SyntaxError exception, that is aware of the
 * module/file the error occurred.
 *
 * @internal
 */
class InternalSyntaxError extends EngineException {

	/** Line number of the syntax error. */
	private int $errorLine;

	/** Position of the syntax error on specified line. */
	private int $linePos;

	/** Position of the syntax error in the whole source string. */
	private int $errorPos;

	/** Specific reason of the syntax error, if specified. */
	private ?string $reason;

	public function __construct(
		int $errorLine,
		int $errorPos,
		int $linePos,
		?string $reason = \null
	) {

		$this->errorLine = $errorLine;
		$this->errorPos = $errorPos;
		$this->linePos = $linePos;
		$this->reason = $reason;

	}

	/**
	 * Get line number of the syntax error.
	 */
	public function getErrorLine(): int {
		return $this->errorLine;
	}

	/**
	 * Get position of the syntax error in the whole source string.
	 */
	public function getErrorPos(): int {
		return $this->errorPos;
	}

	/**
	 * Get position of the syntax error on the specified error line.
	 */
	public function getLinePos(): int {
		return $this->linePos;
	}

	/**
	 * Get specific reason of the syntax error, if specified.
	 */
	public function getReason(): ?string {
		return $this->reason;
	}

}
