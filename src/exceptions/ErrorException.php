<?php

namespace Smuuf\Primi;

class ErrorException extends \RuntimeException {

	public function __construct($msg, $line = false, $pos = false) {

		if (is_array($line)) {
			$msg = sprintf(
				'%s @ "%s".',
				$msg,
				$line['text']
			);
		} elseif ($line !== false && $pos !== false) {
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
