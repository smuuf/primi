<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use \Smuuf\StrictObject;

/**
 * Base abstract class for node handler (always static). The `Node::reduce()`
 * method is used for optional AST node post-process (e.g. reduction) after
 * parsing.
 */
abstract class Handler {

	use StrictObject;

	/**
	 * If true, 'text' node value won't be removed during AST postprocessing.
	 * If false, it will be removed. This can reduce size of cached AST, because
	 * some (most, in fact) nodes don't really need to keep the 'text'.
	 *
	 * @const bool
	 */
	const NODE_NEEDS_TEXT = \false;

	/**
	 * Additional node-type-specific post-process of the AST node provided by
	 * parser. AST node array is passed by reference.
	 */
	public static function reduce(array &$node): void {
		// Nothing is done to the AST node by default.
	}

}
