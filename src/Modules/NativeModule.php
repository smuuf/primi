<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\ValueFriends;

abstract class NativeModule extends ValueFriends {

	/**
	 * This method is called by the importer during import. Resulting array,
	 * which maps variable name to some value, is then used to populate the
	 * module scope.
	 *
	 * @return array<string, AbstractValue|mixed> Pairs that will constitute
	 * the contents of the scope of the module.
	 */
	public function execute(Context $ctx): array {
		return [];
	}

}
