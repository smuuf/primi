<?php

namespace Smuuf\Primi\Structures;

/**
 * Emulate friends visibility.
 *
 * Value and Extension classes extend from this.
 *
 * This way both Value and Extension objects can access the value directly and
 * thus we avoid unnecessary overhead when accessing the value via getter
 * method, which would be often.
 */
abstract class ValueFriends extends \Smuuf\Primi\StrictObject {

	/** @var mixed The actual Value. Whatever it is. */
	protected $value = \null;

}
