<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Location;
use \Smuuf\Primi\Code\Source;
use \Smuuf\Primi\Helpers\StringEscaping;

class SyntaxError extends ErrorException {

	public function __construct(
		Location $location,
		?string $excerpt = \null,
		?string $reason = \null
	) {

		$sanitizedExcerpt = $excerpt
			? StringEscaping::escapeString($excerpt, '"')
			: false;

		$msg = \sprintf(
			"Syntax error%s%s",
			$reason
				? \sprintf(" (%s)", \trim($reason))
				: '',
			$sanitizedExcerpt
				? \sprintf(" near \"%s\"", \trim($sanitizedExcerpt))
				: ''
		);

		parent::__construct($msg, $location);

	}

	public static function fromInternal(
		InternalSyntaxError $e,
		Source $source
	): SyntaxError {

		// Show a bit of code where the syntax error occurred.
		// And don't ever have negative start index (that would take characters
		// indexed from the end).
		$excerpt = \mb_substr(
			$source->getSourceCode(),
			max($e->getErrorPos() - 5, 0),
			10
		);

		return new SyntaxError(
			new Location(
				$source->getSourceId(),
				$e->getErrorLine(),
				$e->getLinePos()
			),
			$excerpt,
			$e->getReason()
		);

	}

}
