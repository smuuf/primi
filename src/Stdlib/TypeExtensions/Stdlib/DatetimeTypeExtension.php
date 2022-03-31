<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions\Stdlib;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Values\StringValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\InstanceValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Structures\CallArgs;

class DatetimeTypeExtension extends TypeExtension {

	/** @const string Timestamp attr name. */
	private const ATTR_TS = '_ts';

	/**
	 * @primi.function(call-convention: object)
	 */
	public static function __init__(CallArgs $args): void {

		[$self, $dateString] = $args->extractPositional(2, 1);

		if ($dateString) {
			Func::allow_argument_types(2, $dateString, StringValue::class);
			$str = $dateString->getStringValue();
		} else {
			$str = 'now';
		}

		$timestamp = \strtotime($str);
		if ($timestamp === \false) {
			throw new RuntimeError("Unable to parse date from string '$str'");
		}

		$self->attrSet(self::ATTR_TS, Interned::number((string) $timestamp));

	}

	/**
	 * @primi.function(no-stack, inject-context, call-convention: object)
	 */
	public static function __op_sub__(
		Context $ctx,
		CallArgs $args
	): AbstractValue {

		[$self, $other] = $args->extractPositional(2);

		$durationType = $ctx->getImporter()
			->getModule('std.datetime')
			->attrGet('Duration');

		$selfType = $self->getType();
		Func::allow_argument_types(2, $other, $selfType, $durationType);

		// Subtracting Datetime (other) from Datetime (self).
		if ($selfType === $other->getType()) {

			$duration = $self->attrGet(self::ATTR_TS)
				->doSubtraction($other->attrGet(self::ATTR_TS));

			// By invoking the class we're gonna create a new instance.
			return $durationType->invoke($ctx, new CallArgs([$duration]));

		}

		// Subtracting Duration (other) from Datetime (self).
		$newTimestamp = $self->attrGet(self::ATTR_TS)
			->doSubtraction($other->attrGet('total_seconds'));

		$newDatetime = new InstanceValue($selfType, $ctx);
		$newDatetime->attrSet(self::ATTR_TS, $newTimestamp);

		return $newDatetime;

	}

	/**
	 * @primi.function(no-stack, inject-context, call-convention: object)
	 */
	public static function __op_add__(
		Context $ctx,
		CallArgs $args
	): AbstractValue {

		[$self, $other] = $args->extractPositional(2);

		$durationType = $ctx->getImporter()
			->getModule('std.datetime')
			->attrGet('Duration');

		Func::allow_argument_types(2, $other, $durationType);

		// Subtracting Duration (other) from Datetime (self).
		$newTimestamp = $self->attrGet(self::ATTR_TS)
			->doAddition($other->attrGet('total_seconds'));

		$newDatetime = new InstanceValue($self->getType(), $ctx);
		$newDatetime->attrSet(self::ATTR_TS, $newTimestamp);

		return $newDatetime;

	}

	/**
	 * @primi.function
	 */
	public static function format(
		AbstractValue $self,
		StringValue $format
	): AbstractValue {

		$timestamp = (int) $self->attrGet(self::ATTR_TS)->getStringValue();
		return Interned::string(\date($format->getStringValue(), $timestamp));

	}

	/**
	 * @primi.function(call-convention: object)
	 */
	public static function __op_eq__(CallArgs $args): AbstractValue {

		[$self, $other] = $args->extractPositional(2);

		if ($other->getType() !== $self->getType()) {
			return Interned::constNotImplemented();
		}

		$equal = $self->attrGet(self::ATTR_TS)
			->isEqualTo($other->attrGet(self::ATTR_TS));

		return Interned::bool($equal);

	}

}
