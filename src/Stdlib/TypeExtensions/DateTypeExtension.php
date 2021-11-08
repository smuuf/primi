<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\InstanceValue;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;

class DateTypeExtension extends TypeExtension {

	/**
	 * @primi.function
	 */
	public static function __init__(
		AbstractValue $self,
		StringValue $dateString
	): void {

		$str = $dateString->getStringValue();
		$unix = \strtotime($str);
		if ($unix === \false) {
			throw new RuntimeError("Unable to parse date from string '$str'");
		}

		$self->attrSet('unix', Interned::number($unix));

	}

	/**
	 * @primi.function
	 */
	public static function add(
		AbstractValue $self,
		AbstractValue $other
	): AbstractValue {

		$newUnix = $self->attrGet('unix')->doAddition($other->attrGet('unix'));
		$newInstance = new InstanceValue($self->getType());
		$newInstance->attrSet('unix', $newUnix);

		return $newInstance;

	}

	/**
	 * @primi.function
	 */
	public static function format(
		AbstractValue $self,
		StringValue $format
	): AbstractValue {

		$unix = (int) $self->attrGet('unix')->getStringValue();
		return Interned::string(strftime($format->getStringValue(), $unix));

	}

}
