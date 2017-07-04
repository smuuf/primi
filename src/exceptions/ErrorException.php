<?php

namespace Smuuf\Primi;

class ErrorException extends InternalException {

	public function __construct($msg, $line = false, $pos = false) {

		if (is_array($line)) {
			$msg = sprintf(
				"%s @ code: %s",
				$msg,
				$line['text']
			);
		} elseif ($line !== false && $pos !== false) {
			$msg = sprintf(
				'%s @ line %s, position %s',
				$msg,
				$line,
				$pos
			);
		}

		parent::__construct($msg);

	}

}
