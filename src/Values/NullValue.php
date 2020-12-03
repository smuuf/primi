<?php

declare(strict_types=1);

namespace Smuuf\Primi\Values;

use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Stats;

class NullValue extends AbstractValue {

	const TYPE = "null";

	/** @var self Stored singleton for all null values. */
	private static $interned = null;

	public static function build($_ = null) {
		return self::$interned ?? (self::$interned = new self);
	}

	public function __construct() {
		Stats::add('value_count_null');
	}

	public function getStringRepr(): string {
		return "null";
	}

	public function hash(): string {
		return 'n';
	}

	public function isTruthy(): bool {
		return \false;
	}

	public function isEqualTo(AbstractValue $right): ?bool {

		if (!$right instanceof self) {
			return \null;
		}

		return $this->value === $right->value;

	}

}
