<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\Modules;

use Smuuf\Primi\Repl;
use Smuuf\Primi\Context;
use Smuuf\Primi\Extensions\PrimiFunc;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Values\NullValue;
use Smuuf\Primi\Values\BoolValue;
use Smuuf\Primi\Values\TupleValue;
use Smuuf\Primi\Values\NumberValue;
use Smuuf\Primi\Values\StringValue;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Values\IteratorFactoryValue;
use Smuuf\Primi\Values\ListValue;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Modules\NativeModule;
use Smuuf\Primi\Structures\CallArgs;
use Smuuf\Primi\Modules\AllowedInSandboxTrait;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Stdlib\StaticTypes;

return new
/**
 * Internal module where _built-ins_ are stored.
 *
 * This module doesn't need to be imported - its contents are available by
 * default in every scope.
 */
class extends NativeModule {

	use AllowedInSandboxTrait;

	public function execute(Context $ctx): array {

		$all = [
			StaticTypes::extractBuiltins(),
			StaticExceptionTypes::extractBuiltins(),
		];

		$vars = [];
		foreach (array_merge(...$all) as $getter) {
			$excType = $getter();
			$vars[$excType->getName()] = $excType;
		}

		return $vars;

	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Prints value to standard output.
	 */
	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function print(
		CallArgs $args,
		Context $ctx
	): NullValue {

		$io = $ctx->getStdIoDriver();

		if ($args->isEmpty()) {
			$io->stdout("\n");
			return Interned::null();
		}

		$args = $args->extract(['*args', 'end', 'sep'], ['end', 'sep']);
		$end = $args['end'] ?? Interned::string("\n");
		$sep = $args['sep'] ?? Interned::string(" ");

		$pieces = \array_map(
			static fn($v) => $v->getStringValue(),
			$args['args']->getCoreValue()
		);

		$io->stdout(
			\implode($sep->getStringValue(), $pieces),
			$end->getStringValue()
		);

		return Interned::null();

	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Injects a [REPL](https://en.wikipedia.org/wiki/Read%E2%80%93eval%E2%80%93print_loop)
	 * session for debugging at the specified line.
	 */
	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function debugger(
		CallArgs $args,
		Context $ctx
	): AbstractValue {

		$args->extractPositional(0);
		if ($ctx->getConfig()->getSandboxMode()) {
			Exceptions::set(
				$ctx,
				StaticExceptionTypes::getRuntimeErrorType(),
				"Function 'debugger' disabled in sandbox",
			);
			return Interned::null();
		}

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
	 */
	#[PrimiFunc]
	public static function len(AbstractValue $value): NumberValue {

		$length = $value->getLength();
		if ($length === \null) {
			$type = $value->getTypeName();
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Type '$type' does not support length",
			);
		}

		return Interned::number((string) $value->getLength());

	}

	/**
	 * This function returns `true` if a `bool` value passed into it is `true`
	 * and throws error if it's `false`. Optional `string` description can be
	 * provided, which will be visible in the eventual error message.
	 */
	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function assert(
		CallArgs $cArgs,
		Context $ctx,
	): AbstractValue {

		$args = $cArgs->extract(['val', 'desc'], ['desc']);
		$val = $args['val'];
		$desc = $args['desc'] ?? null;

		if (!$val instanceof BoolValue) {
			Exceptions::set(
				$ctx,
				StaticExceptionTypes::getTypeErrorType(),
				"Expected bool object"
			);
			return Interned::null();
		}

		if ($val->value === \true) {
			return Interned::bool(\true);
		}

		// Wrap the description string into parentheses if it's not empty.
		$desc = ($desc && $desc->value !== '') ? " ($desc->value)" : '';

		Exceptions::set(
			$ctx,
			StaticExceptionTypes::getAssertionErrorType(),
			\sprintf("Assertion failed%s", $desc),
		);
		return Interned::null();

	}

	/**
	 * This function returns `true` if a `bool` value passed into it is `true`
	 * and throws error if it's `false`. Optional `string` description can be
	 * provided, which will be visible in the eventual error message.
	 */
	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function range(CallArgs $args): IteratorFactoryValue {

		$args = $args->extract(['start', 'end', 'step'], ['end', 'step']);

		// No explicit 'end' argument? That means 'start' actually means 'end'.
		if (!isset($args['end'])) {
			$end = $args['start']->getCoreValue();
			$start = '0';
		} else {
			$start = $args['start']->getCoreValue();
			$end = $args['end']->getCoreValue();
		}

		$step = isset($args['step']) ? $args['step']->getCoreValue() : '1';

		if (
			!Func::is_round_int($start)
			|| !Func::is_round_int($end)
			|| !Func::is_round_int($step)
		) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"All numbers passed to range() must be integers",
			);
		}

		if ($step <= 0) {
			Exceptions::piggyback(
				StaticExceptionTypes::getTypeErrorType(),
				"Range must have a non-negative non-zero step",
			);
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

		return new IteratorFactoryValue(
			// Short closure syntax gives latest PHPStan a headache and
			// reports some nonsense about the function "should not return
			// anything".
			function() use ($gen, $start, $end, $step): \Generator {
				return $gen((int) $start, (int) $end, (int) $step);
			},
			'range'
		);

	}

	/**
	 * Return list of names of attributes present in an object.
	 */
	#[PrimiFunc]
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
	 */
	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function enumerate(CallArgs $args): IteratorFactoryValue {

		[$iterable, $start] = $args->extractPositional(2, 1);
		$start ??= Interned::number('0');

		if (!$start instanceof NumberValue) {
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Start must be a number",
			);
		}

		$counter = $start->getStringValue();
		$it = function($iterable, $counter): \Generator {

			$iter = $iterable->getIterator();
			if ($iter === \null) {
				Exceptions::piggyback(
					StaticExceptionTypes::getTypeErrorType(),
					"Passed value is not iterable",
				);
			}

			foreach ($iter as $item) {
				yield new TupleValue([
					Interned::number((string) $counter),
					$item
				]);
				$counter++;
			}

		};

		return new IteratorFactoryValue(
			// Short closure syntax gives latest PHPStan a headache and
			// reports some nonsense about the function "should not return
			// anything".
			function() use ($it, $iterable, $counter): \Generator {
				return $it($iterable, $counter);
			},
			'enumerate'
		);

	}

	/**
	 * Returns `true` if object has an attribute with specified name.
	 */
	#[PrimiFunc]
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
	 */
	#[PrimiFunc(callConv: PrimiFunc::CONV_CALLARGS)]
	public static function getattr(
		CallArgs $args,
		Context $ctx,
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
		Exceptions::set(
			$ctx,
			StaticExceptionTypes::getAttributeErrorType(),
			"Object of type '$typeName' has no attribute '$attrName'",
		);
	}

};
