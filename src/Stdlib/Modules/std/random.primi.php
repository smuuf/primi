<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\Modules;

use Smuuf\Primi\Values\NumberValue;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Modules\NativeModule;
use Smuuf\Primi\Modules\AllowedInSandboxTrait;
use Smuuf\Primi\Extensions\PrimiFunc;

return new
/**
 * Functions and tools for working with randomness.
 */
class extends NativeModule {

	use AllowedInSandboxTrait;

	/**
	 * Return cryptographically secure random integer from a specified range
	 * such that `a <= N <= b`.
	 *
	 * The maximum and minimum allowed range bound is based on platform
	 * minimum and maximum `int` size.
	 */
	#[PrimiFunc]
	public static function randint(NumberValue $a, NumberValue $b): NumberValue {

		$strA = $a->getStringValue();
		$strB = $b->getStringValue();

		if (!Func::is_round_int($strA)) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Lower bound number is not an integer",
			);
		}

		if (!Func::is_round_int($strB)) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Upper bound number is not an integer",
			);
		}

		if (\bccomp($strA, $strB, 1) > 0) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Second argument is lower than the first",
			);
		}

		if (
			\bccomp($strA, (string) \PHP_INT_MAX) === 1
			|| \bccomp($strA, (string) \PHP_INT_MIN) === -1
		) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				\sprintf(
					"First argument is not in allowed range from %d to %d",
					\PHP_INT_MIN,
					\PHP_INT_MAX,
				),
			);
		}

		if (
			\bccomp($strB, (string) \PHP_INT_MAX) === 1
			|| \bccomp($strB, (string) \PHP_INT_MIN) === -1
		) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				\sprintf(
					"First argument is not in allowed range from %d to %d",
					\PHP_INT_MIN,
					\PHP_INT_MAX,
				),
			);
		}

		return new NumberValue((string) \random_int(
			(int) $a->getStringValue(),
			(int) $b->getStringValue(),
		));

	}

};
