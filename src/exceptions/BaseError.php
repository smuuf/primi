<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

/**
 * All errors that Primi knows will extend this base error exception class.
 */
abstract class BaseError extends BaseException {

	private const DEFAULT_MSG = 'Unknown error';

	public function __construct(
		string $msg = self::DEFAULT_MSG,
		$line = \false,
		$pos = \false
	) {

		// Second argument might be a node from AST tree, so extract position
		// from the node.
		if (\is_array($line)) {
			$pos = $line['_p'] ?? \false;
			$line = $line['_l'] ?? \false;
		}

		if ($line !== \false && $pos !== \false) {
			$msg = \sprintf('%s @ line %s, position %s', $msg, $line, $pos);
		}

		parent::__construct($msg);

	}

}
