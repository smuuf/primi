<?php

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\ISupportsComparison;
use \Smuuf\Primi\Helpers\Common;
use \Smuuf\Primi\Structures\NumberValue;
use \Smuuf\Primi\Structures\NullValue;

class BoolValue extends Value implements ISupportsComparison {

	const TYPE = "bool";

	public function __construct(bool $value) {
		$this->value = $value;
	}

	public function getStringValue(): string {
		return $this->value ? 'true' : 'false';
	}

	public function doComparison(string $op, Value $right): BoolValue {

		Common::allowTypes(
			$right,
			self::class,
			NumberValue::class,
			NullValue::class
		);

		$l = $this->value;
		$r = $right->value;

		switch ($op) {
			case "==":
				return new BoolValue($l == $r);
			case "!=":
				return new BoolValue($l != $r);
			case ">":
				return new BoolValue($l > $r);
			case "<":
				return new BoolValue($l < $r);
			case ">=":
				return new BoolValue($l >= $r);
			case "<=":
				return new BoolValue($l <= $r);
			default:
				throw new \TypeError;
		}

	}

}
