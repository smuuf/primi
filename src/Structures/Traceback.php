<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use Smuuf\StrictObject;

class Traceback {

	use StrictObject;

	/**
	 * @param list<array{string, string, OpLocation}> $framelist
	 */
	public function __construct(
		public readonly array $framelist,
	) {}

}
