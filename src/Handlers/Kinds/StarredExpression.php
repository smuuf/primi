<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use Smuuf\Primi\Compiler\Compiler;
use Smuuf\Primi\Handlers\Handler;

class StarredExpression extends Handler {

	public const STARS_NONE = 0;
	public const STARS_ONE = 1;
	public const STARS_TWO = 2;

	public static function compile(Compiler $bc, array $node): void {
		$bc->inject($node['expr']);
	}

}
