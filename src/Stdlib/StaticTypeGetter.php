<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class StaticTypeGetter {

	public const INJECT_AS_BUILTIN = 1;

	public function __construct(
		public readonly int $flags = 0,
	) {}

}
