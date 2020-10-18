<?php

declare(strict_types=1);

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\ListValue;
use \Smuuf\Primi\Structures\NumberValue;

class StandardExtension extends Extension {

	/**
	 * Returns length of a value.
	 *
	 * ```js
	 * "hello, Česká Třebová".len() == 20
	 * len(123456) == 6
	 * [1, 2, 3].len() == 3
	 * len({'a': 1, 'b': 'c'}) == 2
	 * ```
	 */
	public static function len(Value $value): NumberValue {

		$length = $value->getLength();
		if ($length === null) {
			$type = $value::TYPE;
			throw new RuntimeError("Type '$type' does not support length.");
		}

		return new NumberValue((string) $value->getLength());

	}

	public static function range(
		NumberValue $start,
		?NumberValue $end = \null,
		?NumberValue $step = \null
	): ListValue {

		if (
			!Func::is_round_int((string) $start->value)
			|| ($end && !Func::is_round_int((string) $end->value))
			|| ($step && !Func::is_round_int((string) $step->value))
		) {
			throw new RuntimeError("All arguments must be integers.");
		}

		// If only one agrument is passed, the range will go from 0 to that
		// number.
		if ($end === \null) {
			$range = \range(0, $start->value);
		} else {
			$range = \range(
				$start->value,
				$end->value,
				$step ? $step->value : 1
			);
		}

		return new ListValue(
			\array_map([Value::class, 'buildAutomatic'], $range)
		);

	}

}
