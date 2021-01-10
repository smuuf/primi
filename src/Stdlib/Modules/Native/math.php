<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Extensions\Module;

/**
 * Native 'math' module.
 */
return new class extends Module {

	public function execute(Context $ctx): array {

		return [
			'PI' => NumberValue::build('3.14159265358979323846'),
			'sin' => [self::class, 'sin'],
			'cos' => [self::class, 'cos'],
			'tan' => [self::class, 'tan'],
			'atan' => [self::class, 'atan'],
		];

	}

	/**
	 * Returns the sine of number `n` specified in radians.
	 */
	public static function sin(NumberValue $n): NumberValue {
		$result = (string) \sin((float) $n->value);
		return new NumberValue(Func::scientific_to_decimal($result));
	}

	/**
	 * Returns the cosine of number `n` specified in radians.
	 */
	public static function cos(NumberValue $n): NumberValue {
		$result = (string) \cos((float) $n->value);
		return new NumberValue(Func::scientific_to_decimal($result));
	}

	/**
	 * Returns the tangent of number `n` specified in radians.
	 */
	public static function tan(NumberValue $n): NumberValue {
		$result = (string) \tan((float) $n->value);
		return new NumberValue(Func::scientific_to_decimal($result));
	}

	/**
	 * Returns the arc tangent of number `n` specified in radians.
	 */
	public static function atan(NumberValue $n): NumberValue {
		$result = (string) \atan((float) $n->value);
		return new NumberValue(Func::scientific_to_decimal($result));
	}

};
