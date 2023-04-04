<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers;

use Smuuf\StrictObject;

/**
 * Base abstract class for node handler (always static). The `Node::reduce()`
 * method is used for optional AST node post-process (e.g. reduction) after
 * parsing.
 */
abstract class Handler {

	use StrictObject;

	/**
	 * Additional node-type-specific post-process of the AST node provided by
	 * parser. AST node array is passed by reference.
	 *
	 * @param array $node
	 * @phpstan-param TypeDef_AstNode $node
	 */
	public static function reduce(array &$node): void {

		// Nothing is done to the AST node by default.

	}

}
