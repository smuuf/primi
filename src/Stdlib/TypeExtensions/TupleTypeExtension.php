<?php

namespace Smuuf\Primi\Stdlib\TypeExtensions;

use \Smuuf\Primi\Ex\TypeError;
use \Smuuf\Primi\Values\TypeValue;
use \Smuuf\Primi\Values\TupleValue;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Extensions\TypeExtension;
use \Smuuf\Primi\Stdlib\StaticTypes;

class TupleTypeExtension extends TypeExtension {

	/**
	 * @primi.func(no-stack)
	 */
	public static function __new__(
		TypeValue $type,
		?AbstractValue $value = \null
	): TupleValue {

		if ($type !== StaticTypes::getTupleType()) {
			throw new TypeError("Passed invalid type object");
		}

		// Default value for a new number is 0.
		if ($value === \null) {
			return new TupleValue;
		}

		$iter = $value->getIterator();
		if ($iter === \null) {
			throw new TypeError('tuple() argument must be iterable');
		}

		return new TupleValue(\iterator_to_array($iter));

	}

}
