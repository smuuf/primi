<?php

namespace Smuuf\Primi;

class ErrorException extends InternalException {

	public function __construct($msg, $line = false, $pos = false) {

		if (is_array($line)) {

			if (isset($line['line'], $line['pos'])) {
				$position = sprintf(
					'line %s, position %s, ',
					$line['line'],
					$line['pos']
				);
			}

			// Second argument might be a node from AST tree, so we don't have exact line and position,
			// but we can display a piece of code that caused the error.
			$msg = sprintf(
				"%s @ %scode: %s",
				$msg,
				$position ?? null,
				$line['text']
			);

		} elseif ($line !== false && $pos !== false) {

			// If line and position were provided, we can display it with the error so the user knows where to look.
			$msg = sprintf(
				'%s @ line %s, position %s',
				$msg,
				$line,
				$pos
			);

		}

		parent::__construct("ERR: $msg");

	}

}
