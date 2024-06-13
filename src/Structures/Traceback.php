<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use Smuuf\StrictObject;
use Smuuf\Primi\Code\OpLocation;
use Smuuf\Primi\Values\ModuleValue;

class Traceback {

	use StrictObject;

	/**
	 * @param list<array{string, ModuleValue, OpLocation}> $framelist
	 */
	public function __construct(
		public readonly array $framelist,
	) {}

}
