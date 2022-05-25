<?php

declare(strict_types=1);

namespace Smuuf\Primi\Extensions;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Ex\EngineError;

/**
 * PHP attribute that is to be used to mark PHP extension methods from which
 * should Primi engine create Primi functions.
 */
#[\Attribute(\Attribute::TARGET_FUNCTION | \Attribute::TARGET_METHOD)]
class PrimiFunc {

	use StrictObject;

	public const CONV_CALLARGS = 0x01;
	public const CONV_NATIVE = 0x02;

	private const VALID_CONVENTIONS = [
		self::CONV_CALLARGS,
		self::CONV_NATIVE
	];

	public function __construct(
		private bool $toStack = \false,
		private int $callConv = self::CONV_NATIVE,
	) {
		if (!\in_array($callConv, self::VALID_CONVENTIONS)) {
			throw new EngineError("Invalid callConv argument");
		}
	}

	public function hasToStack(): bool {
		return $this->toStack;
	}

	public function hasCallConv(int $callConv): bool {
		return $this->callConv === $callConv;
	}

}
