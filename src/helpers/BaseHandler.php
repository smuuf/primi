<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers;

use \Smuuf\Primi\StrictObject;

/**
 * Base class for node handler (always static). The static `Node::reduce()`
 * method is used for optional AST node post-process (e.g. reduction) after
 * parsing.
 */
abstract class BaseHandler extends StrictObject {

	const NODE_NEEDS_TEXT = \false;

	/**
	 * Post-process the AST node provided by parser. AST node array is passed by
	 * reference.
	 */
	public static function reduce(array &$node): void {
		// Nothing is done to the AST node by default.
	}

}
