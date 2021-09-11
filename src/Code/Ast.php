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

	/** Abstract syntax tree as (nested) array. */
	private array $tree;

	public function __construct(array $ast) {
		$this->tree = $ast;
	}

	public function getTree(): array {
		return $this->tree;
	}

}
