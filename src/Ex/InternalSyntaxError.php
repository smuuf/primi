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

	/** Line number of the syntax error. **/
	private int $errorLine;

	/** Excerpt string around where the error was. */
	private string $excerpt;

	public function __construct(
		int $errorLine,
		string $excerpt = ''
	) {

		$this->errorLine = $errorLine;
		$this->excerpt = $excerpt;

		$msg = \sprintf(
			"Syntax error %s%s",
			$excerpt ? \sprintf("near '%s' ", \trim($excerpt)) : '',
			\sprintf("on line %d", $errorLine)
		);

		parent::__construct($msg);

	}

	/**
	 * Get line number of the syntax error.
	 */
	public function getErrorLine(): int {
		return $this->errorLine;
	}

	/**
	 * Get excerpt from the location of the error.
	 */
	public function getExcerpt(): string {
		return $this->excerpt;
	}

}
