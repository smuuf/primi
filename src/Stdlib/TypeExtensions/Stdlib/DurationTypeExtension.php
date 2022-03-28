<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions\Stdlib;

use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Values\NumberValue;
use \Smuuf\Primi\Extensions\TypeExtension;
use Smuuf\Primi\Helpers\Interned;
use \Smuuf\Primi\Structures\CallArgs;

class DurationTypeExtension extends TypeExtension {

	/**
	 * @primi.function(call-convention: object)
	 */
	public static function __init__(CallArgs $args): void {

		[$self, $totalSec] = $args->extractPositional(2);
		Func::allow_argument_types(2, $totalSec, NumberValue::class);

		$self->attrSet('total_seconds', $totalSec);

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

}
