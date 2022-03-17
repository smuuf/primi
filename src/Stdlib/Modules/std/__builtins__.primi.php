<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Repl;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\LookupError;
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

		$args = $callArgs->extract(['*args', 'end', 'sep'], ['end', 'sep']);

		$end = $args['end'] ?? Interned::string("\n");
		$sep = $args['sep'] ?? Interned::string(" ");

		$pieces = \array_map(
			fn($v) => $v->getStringValue(),
			$args['args']->getInternalValue()
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

		$args = $callArgs->extract(['start', 'end', 'step'], ['end', 'step']);

		// No explicit 'end' argument? That means 'start' actually means 'end'.
		if (!isset($args['end'])) {
			$end = $args['start']->getInternalValue();
			$start = '0';
		} else {
			$start = $args['start']->getInternalValue();
			$end = $args['end']->getInternalValue();
		}

		$step = isset($args['step']) ? $args['step']->getInternalValue() : '1';

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
	 * list(enumerate(b_list, -5)) == [(-5, 'a'), (-4, 'b'), (-3, 123), (-2, false)]
	 * ```
	 *
	 * @primi.function(no-stack, call-convention: object)
	 * @primi.function.arg(name: iterable, type: iterable)
	 * @primi.function.arg(name: start, type: number, default: 0)
	 */
	public static function enumerate(CallArgs $callArgs): GeneratorValue {

		[$iterable, $start] = $callArgs->extractPositional(2, 1);
		$start ??= Interned::number('0');

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
	public static function hasattr(
		AbstractValue $obj,
		StringValue $name
	): BoolValue {
		return Interned::bool(isset($obj->attrs[$name->getStringValue()]));
	}

	/**
	 * Returns value of object's attribute with specified name. If the object
	 * has no attribute of that name, error is thrown.
	 *
	 * If the optional `default` argument is specified its value is returned
	 * instead of throwing an error.
	 *
	 * @primi.function(no-stack, call-convention: object)
	 */
	public static function getattr(
		CallArgs $args
	): AbstractValue {

		[$obj, $name, $default] = $args->extractPositional(3, 1);

		$attrName = $name->getStringValue();
		if ($attrValue = $obj->attrGet($attrName)) {
			return $attrValue;
		}

		if ($default !== \null) {
			return $default;
		}

		$typeName = $obj->getTypeName();
		throw new LookupError(
			"Object of type '$typeName' has no attribute '$attrName'");

	}

};
