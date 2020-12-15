<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib;

use \Smuuf\Primi\Repl;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Extensions\Extension;

class CliExtension extends Extension {

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Prints value to standard output.
	 */
	public static function print(
		AbstractValue $value = null,
		BoolValue $nl = \null
	): NullValue {

		$text = $value === null ? '' : $value->getStringValue();
		$nl = $nl !== \null ? $nl->isTruthy() : \true; // Newline by default.
		echo $text . ($nl ? "\n" : '');

		return NullValue::build();

	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Injects a [REPL](https://en.wikipedia.org/wiki/Read%E2%80%93eval%E2%80%93print_loop)
	 * session for debugging at the specified line.
	 *
	 * @injectContext
	 * @noStack
	 */
	public static function debugger(Context $ctx): AbstractValue {
		$repl = new Repl('<debugger>');
		return $repl->start($ctx) ?? NullValue::build();
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Return traceback as a list.
	 *
	 * @injectContext
	 * @noStack
	 */
	public static function get_traceback(Context $ctx): ListValue {
		return AbstractValue::buildAuto($ctx->getTraceback());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Returns memory peak usage used by Primi _(engine behind the scenes)_ in
	 * bytes.
	 */
	public static function memory_get_peak_usage(): NumberValue {
		return new NumberValue((string) \memory_get_peak_usage());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Returns current memory usage used by Primi _(engine behind the scenes)_
	 * in bytes.
	 */
	public static function memory_get_usage(): NumberValue {
		return new NumberValue((string) \memory_get_peak_usage());
	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * This function returns `true` if a `bool` value passed into it is `true`
	 * and throws error if it's `false`. Optional `string` description can be
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

		return BoolValue::build(\true);

	}

}
