<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\Extensions;

use \Smuuf\Primi\Repl;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Values\NumberValue;
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
		$repl = new Repl('debugger');
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

}
