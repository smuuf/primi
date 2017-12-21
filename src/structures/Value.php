<?php

namespace Smuuf\Primi\Structures;

abstract class Value extends \Smuuf\Primi\StrictObject {

	const TYPE = "__no_type__";

	const TYPE_NUMBER = 0x01;
	const TYPE_STRING = 0x02;
	const TYPE_BOOL = 0x03;
	const TYPE_ARRAY = 0x04;
	const TYPE_REGEX = 0x05;

	/** @var mixed Value **/
	protected $value;

	public static function buildAutomatic($value) {

		switch (true) {
			case \is_bool($value):
				return new BoolValue($value);
			case \is_array($value):
				return new ArrayValue(array_map([self::class, 'buildAutomatic'], $value));
			case NumberValue::isNumeric($value): // Must be after "is_array" case.
				return new NumberValue($value);
			default:
				return new StringValue($value);
		}

	}

	public function getPhpValue() {
		return $this->value;
	}

	/**
	 * Throw new TypeException when the value does not match any of the types provided.
	 * Use this when allowing only certain types of values in call*() methods.
	 *
	 * This is needed because of PHP's imperfect type-hinting system which does not support union types.
	 *
	 * @throws \TypeException
	 */
	protected static function allowTypes(?Value $value, string ...$types) {

		foreach ($types as $type) {
			if ($value instanceof $type) {
				return; // If any of the instanceof checks is true, the type is allowed.
			}
		}

		// The value did not match any of the types provided.
		throw new \TypeError;

	}

}
