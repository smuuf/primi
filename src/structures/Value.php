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
			case NumberValue::isNumeric($value):
				return new NumberValue($value);
			case \is_bool($value):
				return new BoolValue($value);
			default:
				return new StringValue($value);
		}

	}

	public function getPhpValue() {
		return $this->value;
	}

}
