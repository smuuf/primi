<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

/**
 * StringEscaping can throw this.
 *
 * @internal
 */
class WrongEscapeSequenceException extends EngineException {

	/**
	 * @param string $piece Which character was tried to be used as part of
	 *     escape sequence? For example "č" in "\č".
	 */
	public function __construct(
		public readonly string $char,
	) {
		parent::__construct("Unrecognized string escape sequence '{$char}'");
	}

}
