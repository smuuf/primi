<?php

declare(strict_types=1);

namespace Smuuf\Primi\Psl;

use Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Extension;
use Smuuf\Primi\ISupportsLength;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\BoolValue;
use Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\StringValue;

class StandardExtension extends Extension {

	/**
	 * Prints value if in Primi is run in CLI. Does nothing otherwise.
	 * Always returns null.
	 */
	public static function print(Value $value): NullValue {

		if (PHP_SAPI === 'cli') {
			echo $value->getStringValue() . "\n";
		}

		return new NullValue;

	}

	/**
	 * This function returns true if anything passed into it is true and throws
	 * error if not. Optional string decription can be provided and it will
	 * be shown in the eventual error message.
	 */
	public static function assert(BoolValue $truth, ?StringValue $desc = null): BoolValue {

		if ($truth->value !== true) {
			$desc = ($desc && $desc->value !== '') ? " ($desc->value)" : '';
			throw new ErrorException(sprintf("Assertion failed%s", $desc));
		}

		return new BoolValue(true);

	}

	public static function length(ISupportsLength $value): NumberValue {
		return new NumberValue((string) $value->getLength());
	}

}
