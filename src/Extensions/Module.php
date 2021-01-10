<?php

namespace Smuuf\Primi\Extensions;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\ValueFriends;

abstract class Module extends ValueFriends {

	/**
	 * This method is called by the importer during import. Resulting array,
	 * which maps variable name to some value, is then used to populate the
	 * module contents.
	 *
	 * @return array<string, mixed|AbstractValue> Pairs that will constitute
	 * the contents of the module.
	 */
	abstract public function execute(Context $ctx): array;

}
