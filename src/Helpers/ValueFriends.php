<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\StrictObject;

/**
 * Emulate friend visibility - extending classes can access internal `$value`
 * property of `AbstractValue` class.
 *
 * AbstractValue and Extension classes use this.
 *
 * This way both AbstractValue and Extension objects can access the value directly and
 * thus we avoid unnecessary overhead when accessing the value via getter
 * method, which would be often.
 */
abstract class ValueFriends {

	use StrictObject;

	/** @var mixed Value itself. */
	protected $value = \null;

	/** Value object attributes. */
	protected array $attrs = [];

}
