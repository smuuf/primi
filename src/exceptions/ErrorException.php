<?php

namespace Smuuf\Primi;

class ErrorException extends \RuntimeException {

	public function __construct($msg, $line = false, $pos = false) {

		if ($line !== false && $pos !== false) {
			$msg = sprintf(
				'%s @ line %s, position %s.',
				$msg,
				$line,
				$pos
			);
		} else {
			$msg = sprintf(
				'%s.',
				$msg
			);
		}

		parent::__construct($msg);

	}

}
