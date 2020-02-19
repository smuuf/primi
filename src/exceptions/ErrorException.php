<?php

namespace Smuuf\Primi;

class ErrorException extends InternalException {

	public function __construct($msg, $line = false, $pos = false) {

		// Second argument might be a node from AST tree, so extract position
		// from the node.
		if (is_array($line)) {
			$pos = $line['pos'] ?? false;
			$line = $line['line'] ?? false;
		}

		if ($line !== false && $pos !== false) {
			$msg = sprintf('%s @ line %s, position %s',	$msg, $line, $pos);
		}

		parent::__construct($msg);

	}

}
