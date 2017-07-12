<?php

namespace Smuuf\Primi\Structures;

abstract class Value extends \Smuuf\Primi\Object {

	const TYPE_NUMBER = 0x01;
	const TYPE_STRING = 0x02;
	const TYPE_BOOL = 0x03;
	const TYPE_ARRAY = 0x04;
	const TYPE_REGEX = 0x05;

	/** @var Number **/
	protected $value;

	public static function build(string $type, $value) {

		switch ($type) {
			case self::TYPE_NUMBER:
				return new NumberValue($value);
			case self::TYPE_STRING:
				return new StringValue($value);
			case self::TYPE_BOOL:
				return new BoolValue($value);
			case self::TYPE_ARRAY:
				return new ArrayValue($value);
			case self::TYPE_REGEX:
				return new RegexValue($value);
			default:
				throw new \RuntimeException("Cannot build value of unknown type '$type'.");
		}

	}

	public function getPhpValue() {
		return $this->value;
	}

}
