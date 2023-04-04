<?php

declare(strict_types=1);

namespace Smuuf\Primi\Code;

use Smuuf\StrictObject;

/**
 * Bytecode line info (line position and offset).
 *
 * @internal
 */
class OpLocation {

	use StrictObject;

	public function __construct(
		public readonly int $line,
		public readonly int $pos,
	) {}

}
