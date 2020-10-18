<?php

declare(strict_types=1);

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Repl;
use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\StringValue;

class CliExtension extends Extension {

	/**
	 * **Only in [CLI](https://w.wiki/QPE)**.
	 * Prints value to standard output.
	 */
	public static function print(
		Value $value,
		BoolValue $nl = null
	): NullValue {

		$nl = $nl !== null ? $nl->isTruthy() : true; // Newline by default.
		echo $value->getStringValue() . ($nl ? "\n" : '');

		return new NullValue;

	}

	/**
	 * **Only in [CLI](https://w.wiki/QPE)**.
	 * Injects a REPL session for debugging at the specified line.
	 */
	public function debugger(): Value {

		$repl = new Repl($this->getContext()->getInterpreter());
		return $repl->start() ?? new NullValue;

	}

	/**
	 * Returns memory peak usage used by Primi (or rather PHP behind it) in \
	 * bytes.
	 */
	public static function memory_get_peak_usage(): NumberValue {
		return new NumberValue((string) memory_get_peak_usage());
	}

	/**
	 * Returns current usage used by Primi (or rather PHP behind it) in \
	 * bytes.
	 */
	public static function memory_get_usage(): NumberValue {
		return new NumberValue((string) memory_get_peak_usage());
	}

	/**
	 * This function returns true if a boolean value passed into it is `true` \
	 * and throws error if it's `false`. Optional string decription can be \
	 * provided, which will be visible in the eventual error message.
	 */
	public static function assert(
		BoolValue $assumption,
		?StringValue $description = \null
	): BoolValue {

		$desc = $description;
		if ($assumption->value !== \true) {
			$desc = ($desc && $desc->value !== '') ? " ($desc->value)" : '';
			throw new RuntimeError(sprintf("Assertion failed%s", $desc));
		}

		return new BoolValue(\true);

	}

}
