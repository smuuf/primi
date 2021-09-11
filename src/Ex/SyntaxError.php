<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Location;

class SyntaxError extends ErrorException {

	/** Location of the error. */
	private Location $location;

	public function __construct(
		Location $location,
		string $excerpt = ''
	) {

		$this->location = $location;
		$this->message = \sprintf(
			"Syntax error %s%s",
			$excerpt ? \sprintf("near '%s' ", \trim($excerpt)) : '',
			\sprintf("in %s on line %d", $location->getName(), $location->getLine())
		);

	}

	/**
	 * Return object representing location of the error.
	 */
	public function getLocation(): Location {
		return $this->location;
	}

}
