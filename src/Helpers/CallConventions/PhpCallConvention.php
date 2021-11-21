<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\CallConventions;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\ArgumentCountError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Structures\CallArgs;

use \Smuuf\BetterExceptions\BetterException;
use \Smuuf\BetterExceptions\Types\ArgumentTypeError;
use \Smuuf\BetterExceptions\Types\ArgumentCountError as BetterArgumentCountError;

/**
 * Call convention for invoking PHP callables from within Primi code/engine.
 *
 * This convention passes positional args contained in CallArgs object
 * into the PHP callable as variadic arguments.
 *
 * This convention does not support sending kwargs into PHP callable.
 * (...yet? Maybe with PHP 8.1 with named parameters we might support it.)
 *
 * @internal
 */
class PhpCallConvention implements CallConventionInterface {

	use StrictObject;

	private \Closure $closure;

	public function __construct(
		\Closure $closure,
		\ReflectionFunction $rf
	) {

		$this->closure = $closure;
		Func::check_allowed_parameter_types_of_function($rf);

	}

	public function call(
		CallArgs $callArgs,
		?Context $ctx
	): ?AbstractValue {

		$finalArgs = $callArgs->getArgs();
		if ($callArgs->getKwargs()) {
			throw new RuntimeError(
				"Calling native functions with kwargs is not allowed");
		}

		if ($ctx) {
			\array_unshift($finalArgs, $ctx);
		}

		try {

			$result = ($this->closure)(...$finalArgs);

			return $result instanceof AbstractValue
				? $result
				: AbstractValue::buildAuto($result);

		} catch (\TypeError $e) {

			$better = BetterException::from($e);

			// We want to handle only argument type errors. Other errors
			// (for example "return type errors") are a sign of badly used
			// return type hint for PHP function and should bubble up (be
			// rethrown) for the developer to see it.

			if ($better instanceof ArgumentTypeError) {
				$argIndex = $better->getArgumentIndex();
				throw new TypeError(\sprintf(
					"Expected '%s' but got '%s' as argument %d",
					implode('|', $better->getExpected()),
					$finalArgs[$argIndex - 1]->getTypeName(),
					$argIndex
				));
			}

			if ($better instanceof BetterArgumentCountError) {

				// If function requested injecting Context object into its
				// arguments, this should be transparent to the caller and
				// thus we subtract 1 from the number of arguments we report
				// here.
				throw new ArgumentCountError(
					$better->getActual() - ($ctx ? 1 : 0),
					$better->getExpected() - ($ctx ? 1 : 0));

			}

			throw $e;

		}

	}

}

