<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Location;
use \Smuuf\Primi\Helpers\StringEscaping;

class SyntaxError extends ErrorException {

	public function __construct(
		Location $location,
		?string $excerpt = \null,
		?string $reason = \null
	) {

		$sanitizedExcerpt = $excerpt
			? StringEscaping::escapeString($excerpt)
			: false;

		$msg = \sprintf(
			"Syntax error%s%s",
			$reason
				? \sprintf(" (%s)", \trim($reason))
				: '',
			$sanitizedExcerpt
				? \sprintf(" near '%s'", \trim($sanitizedExcerpt))
				: ''
		);

		parent::__construct($msg, $location);

	}

}
