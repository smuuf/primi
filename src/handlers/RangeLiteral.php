<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\ArrayValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\StringValue;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class RangeLiteral extends \Smuuf\Primi\StrictObject implements IHandler {

	public static function handle(array $node, Context $context) {

		try {

			$lValue = HandlerFactory
				::get($node['left']['name'])
				::handle($node['left'], $context);

			$rValue = HandlerFactory
				::get($node['right']['name'])
				::handle($node['right'], $context);

			Common::allowTypes($lValue, NumberValue::class);
			Common::allowTypes($rValue, NumberValue::class);

			// Default step, but allow having the step defined by user.
			$step = 1;
			if (isset($node['step'])) {

				$sValue = HandlerFactory
					::get($node['step']['name'])
					::handle($node['step'], $context);
				Common::allowTypes($sValue, NumberValue::class);

				$step = (int) $sValue->getInternalValue();

			}

		} catch (\TypeError $e) {

			$msg = "Invalid range from '%s' to '%s'";
			$msg = sprintf($msg, $lValue::TYPE, $rValue::TYPE);
			throw new ErrorException($msg, $node);

		}

		$from = (int) $lValue->getInternalValue();
		$to = (int) $rValue->getInternalValue();

		// Handle edge cases of step being incompatible with:
		// 1. The direction of the range.
		// 2. The range end value.
		// Eg. "1..10..2" has an invalid step, because 10 is bigger number than
		// the range 2. The same with "1..10..-5".
		// Also, if the from-to pair is the same number, allow it, since no
		// step value is even utilized.
		$overflow = abs($step) > abs($from - $to);
		if ($overflow && $from !== $to) {
			$msg = "Invalid step '$step' for range";
			$msg = sprintf($msg, $lValue::TYPE, $rValue::TYPE);
			throw new ErrorException($msg, $node);
		}

		$range = range($from, $to, $step);

		return new ArrayValue(
			array_map([Value::class, 'buildAutomatic'], $range)
		);

	}

}
