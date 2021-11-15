<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Repl;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\GeneratorValue;
use \Smuuf\Primi\Values\ListValue;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Modules\NativeModule;
use \Smuuf\Primi\Structures\CallArgs;

return new
/**
 * Internal module where _built-ins_ are stored.
 *
 * This module doesn't need to be imported - its contents are available by
 * default in every scope.
 */
class extends NativeModule {

	public function execute(Context $ctx): array {

		$types = $ctx->getImporter()
			->getModule('std.types')
			->getInternalValue();

		return [
			'object' => $types->getVariable('object'),
			'type' => $types->getVariable('type'),
			'bool' => $types->getVariable('bool'),
			'dict' => $types->getVariable('dict'),
			'list' => $types->getVariable('list'),
			'tuple' => $types->getVariable('tuple'),
			'number' => $types->getVariable('number'),
			'regex' => $types->getVariable('regex'),
			'string' => $types->getVariable('string'),
		];

	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Prints value to standard output.
	 *
	 * @primi.function(no-stack, call-convention: object)
	 */
	public static function print(
		CallArgs $callArgs
	): NullValue {

		if ($callArgs->isEmpty()) {
			echo "\n";
			return Interned::null();
		}

		$end = $callArgs->safeGetKwarg('end', Interned::string("\n"));
		$sep = $callArgs->safeGetKwarg('sep', Interned::string(", "));

		$pieces = \array_map(
			fn($v) => $v->getStringValue(),
			$callArgs->getArgs()
		);

		echo \implode($sep->getStringValue(), $pieces);
		echo $end->getStringValue();

		return Interned::null();

	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Injects a [REPL](https://en.wikipedia.org/wiki/Read%E2%80%93eval%E2%80%93print_loop)
	 * session for debugging at the specified line.
	 *
	 * @primi.function(inject-context, no-stack)
	 */
	public static function debugger(Context $ctx): AbstractValue {
		$repl = new Repl('debugger');
		return $repl->start($ctx) ?? Interned::null();
	}


	/**
	 * Returns length of a value.
	 *
	 * ```js
	 * len("hello, Česká Třebová") == 20
	 * len(123456) == 6
	 * len([1, 2, 3]) == 3
	 * len({'a': 1, 'b': 'c'}) == 2
	 * ```
	 *
	 * @primi.function(no-stack)
	 */
	public static function len(AbstractValue $value): NumberValue {

		$length = $value->getLength();
		if ($length === \null) {
			$type = $value->getTypeName();
			throw new RuntimeError("Type '$type' does not support length");
		}

		return Interned::number((string) $value->getLength());

	}

	/**
	 * This function returns `true` if a `bool` value passed into it is `true`
	 * and throws error if it's `false`. Optional `string` description can be
	 * provided, which will be visible in the eventual error message.
	 *
	 * @primi.function(no-stack)
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

		return Interned::bool(\true);

	}

	/**
	 * This function returns `true` if a `bool` value passed into it is `true`
	 * and throws error if it's `false`. Optional `string` description can be
	 * provided, which will be visible in the eventual error message.
	 *
	 * @primi.function(no-stack)
	 */
	public static function range(
		NumberValue $start,
		?NumberValue $end = \null,
		?NumberValue $step = \null
	) {

		$start = (int) $start->getInternalValue();
		$end = $end ? (int) $end->getInternalValue() : \null;

		// Single argument? Then it represents the end (and start is 0).
		if ($end === \null) {
			$end = $start;
			$start = 0;
		}

		$direction = $end >= $start ? 1 : -1;
		$step = ($step ? (int) $step->getInternalValue() : 1) * $direction;

		$gen = function(int $start, int $end, int $step) {

			$c = $start;
			while (\true) {

				if ($start < $end && $c >= $end) {
					break;
				}

				if ($start > $end && $c <= $end) {
					break;
				}

				yield Interned::number((string) $c);
				$c += $step;

			}

		};

		return new GeneratorValue($gen($start, $end, $step));

	}

	/**
	 * Return list of names of attributes present in an object.
	 *
	 * @primi.function(no-stack)
	 */
	public static function dir(AbstractValue $value) {
		return new ListValue(
			array_map(
				[Interned::class, 'string'],
				$value->dirItems() ?? []
			)
		);
	}

};
