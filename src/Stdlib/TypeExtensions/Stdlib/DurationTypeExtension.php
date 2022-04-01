<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions\Stdlib;

use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Values\InstanceValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Structures\CallArgs;

class DurationTypeExtension extends TypeExtension {

	/** @const string Total seconds attr name. */
	private const ATTR_TOTSEC = 'total_seconds';

	/**
	 * @primi.func(call-conv: callargs)
	 */
	public static function __init__(
		CallArgs $args
	): void {

		[$self, $totalSec] = $args->extractPositional(2);
		Func::allow_argument_types(2, $totalSec, NumberValue::class);

		$self->attrSet(self::ATTR_TOTSEC, $totalSec);

		$totalSecInt = $totalSec->getInternalValue();
		$self->attrSet(
			'days',
			Interned::number((string) floor((int) $totalSecInt / 86400))
		);

		$self->attrSet(
			'seconds',
			Interned::number((string) ((int) $totalSecInt % 86400))
		);

	}

	/**
	 * @primi.func(call-conv: callargs)
	 */
	public static function __op_eq__(
		CallArgs $args
	): AbstractValue {

		[$self, $other] = $args->extractPositional(2);

		if ($other->getType() !== $self->getType()) {
			return Interned::constNotImplemented();
		}

		$equal = $self->attrGet(self::ATTR_TOTSEC)
			->isEqualTo($other->attrGet(self::ATTR_TOTSEC));

		return Interned::bool($equal);

	}

	/**
	 * @primi.func(call-conv: callargs)
	 */
	public static function __op_add__(
		CallArgs $args
	): AbstractValue {

		[$self, $other] = $args->extractPositional(2);

		if ($other->getType() !== $self->getType()) {
			return Interned::constNotImplemented();
		}

		$totalSeconds = $self->attrGet(self::ATTR_TOTSEC)
			->doAddition($other->attrGet(self::ATTR_TOTSEC));

		$new = new InstanceValue($self->getType(), $ctx);
		$new->attrSet(self::ATTR_TOTSEC, $totalSeconds);

		return $new;

	}


	/**
	 * @primi.func(call-conv: callargs)
	 */
	public static function __op_sub__(
		CallArgs $args
	): AbstractValue {

		[$self, $other] = $args->extractPositional(2);

		if ($other->getType() !== $self->getType()) {
			return Interned::constNotImplemented();
		}

		$totalSeconds = $self->attrGet(self::ATTR_TOTSEC)
			->doSubtraction($other->attrGet(self::ATTR_TOTSEC));

		$new = new InstanceValue($self->getType(), $ctx);
		$new->attrSet(self::ATTR_TOTSEC, $totalSeconds);

		return $new;

	}

}
