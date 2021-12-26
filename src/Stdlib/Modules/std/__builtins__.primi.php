<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Repl;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\NullValue;
use \Smuuf\Primi\Values\BoolValue;
use \Smuuf\Primi\Values\TupleValue;
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
		$repl->start($ctx);

		return Interned::null();

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
	 * @primi.function(no-stack, call-convention: object)
	 */
	public static function range(CallArgs $callArgs): GeneratorValue {

		$arg1 = $callArgs->safeGetArg(0);
		$arg2 = $callArgs->safeGetArg(1);
		$arg3 = $callArgs->safeGetArg(2);

		// No explicit 'end' argument? That means 'start' actually means 'end'.
		if ($arg2 === \null) {
			$end = $arg1->getInternalValue();
			$start = '0';
		} else {
			$start = $arg1->getInternalValue();
			$end = $arg2->getInternalValue();
		}

		$step = $arg3 ? $arg3->getInternalValue() : '1';

		if (
			!Func::is_round_int($start)
			|| !Func::is_round_int($end)
			|| !Func::is_round_int($step)
		) {
			throw new RuntimeError(
				"All numbers passed to range() must be integers");
		}

		if ($step <= 0) {
			throw new RuntimeError(
				"Range must have a non-negative non-zero step");
		}

		$direction = $end >= $start ? 1 : -1;
		$step *= $direction;

		$gen = function(int $start, int $end, int $step) {

			if ($start === $end) {
				return;
			}

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

			return;

		};

		return new GeneratorValue($gen((int) $start, (int) $end, (int) $step));

	}

	/**
	 * Return list of names of attributes present in an object.
	 *
	 * @primi.function(no-stack)
	 */
	public static function dir(AbstractValue $value): ListValue {
		return new ListValue(
			array_map(
				[Interned::class, 'string'],
				$value->dirItems() ?? []
			)
		);
	}

	/**
	 * Returns iterator yielding tuples of index and items from an iterator.
	 *
	 * ```js
	 * a_list = ['a', 'b', 123, false]
	 * list(enumerate(a_list)) == [(0, 'a'), (1, 'b'), (2, 123), (3, false)]
	 *
	 * b_list = ['a', 'b', 123, false]
	 * list(enumerate(b_list, start: -5)) == [(-5, 'a'), (-4, 'b'), (-3, 123), (-2, false)]
	 * ```
	 *
	 * @primi.function(no-stack, call-convention: object)
	 * @primi.function.arg(name: iterable, type: iterable)
	 * @primi.function.arg(name: start, type: number, default: 0)
	 */
	public static function enumerate(CallArgs $callArgs): GeneratorValue {

		if ($callArgs->getTotalCount() > 2) {
			throw new RuntimeError("enumerate() takes max 2 arguments");
		}

		$iterable = $callArgs->safeGetArg(0)
			?? $callArgs->getKwarg('iterable');

		$start = $callArgs->safeGetArg(1)
			?? $callArgs->safeGetKwarg('start', Interned::number('0'));

		if (!$start instanceof NumberValue) {
			throw new RuntimeError("Argument 'start' is not a number");
		}

		$iter = $iterable->getIterator();

		if ($iter === \null) {
			throw new RuntimeError("Argument 'iterable' is not iterable");
		}

		$counter = $start->getStringValue();
		$it = function() use ($iter, $counter) {
			foreach ($iter as $item) {
				yield new TupleValue([
					Interned::number((string) $counter),
					$item
				]);
				$counter++;
			}
		};

		return new GeneratorValue($it());

	}

	/**
	 * Returns `true` if object has an attribute with specified name.
	 *
	 * @primi.function(no-stack)
	 */
	public static function hasattr(AbstractValue $obj, StringValue $name): BoolValue {
		return Interned::bool(isset($obj->attrs[$name->getStringValue()]));
	}

};
