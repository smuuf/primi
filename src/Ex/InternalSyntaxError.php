<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use Smuuf\Primi\Location;

class InternalSyntaxError extends ErrorException {

	/** Excerpt string around where the error was. */
	private $excerpt;

	public function __construct(
		int $line,
		//int $position,
		string $excerpt = ''
	) {
		$position = 0;
		$near = $excerpt
			? \sprintf(" near '%s'", \trim($excerpt))
			: '';

		$loc = new Location('', $line, $position);

		$msg = \sprintf("Syntax error%s", $near);
		parent::__construct($msg, $loc);

		$this->excerpt = $excerpt;

	}

	/**
	 * Get excerpt from the location of the error.
	 */
	public function getExcerpt(): string {
		return $this->excerpt;
	}

}
