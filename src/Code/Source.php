<?php

declare(strict_types=1);

namespace Smuuf\Primi\Code;

use \Smuuf\StrictObject;

/**
 * Helper class for representing Primi source code.
 *
 * @internal
 */
class Source {

	use StrictObject;

	/** Source ID meant for human-friendly identification. */
	protected string $sourceId;

	/** Primi source code string. */
	protected string $sourceCode;

	public function __construct(string $code) {

		$id = sprintf("<string %s>", md5($code));
		$this->sourceId = $id;
		$this->sourceCode = $code;

	}

	public function getSourceId(): string {
		return $this->sourceId;
	}

	public function getSourceCode(): string {
		return $this->sourceCode;
	}

}
