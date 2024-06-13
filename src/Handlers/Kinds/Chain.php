<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class Chain extends Handler {

	/**
	 * @phpstan-param TypeDef_AstNode $node
	 */
	public static function compile(Compiler $bc, array $node): void {

		$bc->inject($node['core']);

		if (isset($node['chain'])) {
			$bc->inject($node['chain']);
		}

	}

}
