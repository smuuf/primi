<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Location;

class SyntaxError extends ErrorException {

	public function __construct(
		Location $location,
		?string $excerpt = \null,
		?string $reason = \null
	) {

		$msg = \sprintf(
			"Syntax error%s%s",
			$reason ? \sprintf(" (%s)", \trim($reason)) : '',
			$excerpt ? \sprintf(" near '%s'", \trim($excerpt)) : ''
		);

		parent::__construct($msg, $location);

	}

}
