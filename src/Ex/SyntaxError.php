<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Location;

class SyntaxError extends ErrorException {

	public function __construct(
		Location $location,
		string $excerpt = ''
	) {

		$msg = \sprintf(
			"Syntax error %s%s",
			$excerpt ? \sprintf("near '%s' ", \trim($excerpt)) : '',
			\sprintf("in %s on line %d", $location->getName(), $location->getLine())
		);

		parent::__construct($msg, $location);

	}

}
