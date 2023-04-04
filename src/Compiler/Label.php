<?php

declare(strict_types=1);

namespace Smuuf\Primi\Compiler;

class Label {

	use \Smuuf\StrictObject;

	public function __construct(
		public readonly string $id,
	) {}

}
