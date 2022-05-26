<?php

namespace Smuuf\Primi\Helpers;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Values\AbstractValue;

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

	/**
	 * Attributes of Primi object.
	 *
	 * @var array<string, AbstractValue>
	 */
	protected array $attrs = [];

	/**
	 * Internal attributes of Primi object that are not directly accessible
	 * from Primi userland.
	 *
	 * @var array<string, mixed>
	 */
	public array $internal = [];

}
