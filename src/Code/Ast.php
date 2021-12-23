<?php

declare(strict_types=1);

namespace Smuuf\Primi\Code;

use \Smuuf\StrictObject;

/**
 * Abstract syntax tree object (result from parsing Primi source code)..
 *
 * @see https://en.wikipedia.org/wiki/Abstract_syntax_tree
 * @internal
 */
class Ast {

	use StrictObject;

	/**
	 * Abstract syntax tree as (nested) array.
	 *
	 * @var TypeDef_AstNode
	 */
	private array $tree;

	/**
	 * @param TypeDef_AstNode $ast
	 */
	public function __construct(array $ast) {
		$this->tree = $ast;
	}

	/**
	 * @return TypeDef_AstNode
	 */
	public function getTree(): array {
		return $this->tree;
	}

}
