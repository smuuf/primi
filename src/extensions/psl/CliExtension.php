<?php

declare(strict_types=1);

namespace Smuuf\Primi\Psl;

use \Smuuf\Primi\Repl;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Extension;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Structures\NullValue;
use \Smuuf\Primi\Structures\BoolValue;
use \Smuuf\Primi\Structures\ListValue;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\StringValue;

class CliExtension extends Extension {

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 * Prints value to standard output.
	 */
	public static function print(
		Value $value,
		BoolValue $nl = \null
	): NullValue {

		$nl = $nl !== \null ? $nl->isTruthy() : \true; // Newline by default.
		echo $value->getStringValue() . ($nl ? "\n" : '');

		return new NullValue;

	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 * Injects a [REPL](https://en.wikipedia.org/wiki/Read%E2%80%93eval%E2%80%93print_loop) \
	 * session for debugging at the specified line.
	 *
	 * @injectContext
	 */
	public static function debugger(Context $ctx): Value {

		$repl = new Repl('<debugger>');
		return $repl->start($ctx) ?? new NullValue;

	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 * Return traceback as a list.
	 *
	 * @injectContext
	 */
	public static function get_traceback(Context $ctx): ListValue {
		return Value::buildAutomatic($ctx->getTraceback());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 * Returns memory peak usage used by Primi (or rather PHP behind it) in \
	 * bytes.
	 */
	public static function memory_get_peak_usage(): NumberValue {
		return new NumberValue((string) \memory_get_peak_usage());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 * Returns current usage used by Primi (or rather PHP behind it) in bytes.
	 */
	public static function memory_get_usage(): NumberValue {
		return new NumberValue((string) \memory_get_peak_usage());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
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
			throw new RuntimeError(\sprintf("Assertion failed%s", $desc));
		}

		return new BoolValue(\true);

	}

}
