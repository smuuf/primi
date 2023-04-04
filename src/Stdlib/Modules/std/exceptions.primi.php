<?php

declare(strict_types=1);

namespace Smuuf\Primi\Stdlib\Modules;

use Smuuf\Primi\Context;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Modules\NativeModule;
use Smuuf\Primi\Modules\AllowedInSandboxTrait;

return new
/**
 * Module housing Primi's basic data types.
 */
class extends NativeModule {

	use AllowedInSandboxTrait;

	public function execute(Context $ctx): array {

		$vars = [];
		foreach (StaticExceptionTypes::extractAll() as $getter) {
			$excType = $getter();
			$vars[$excType->getName()] = $excType;
		}

		return $vars;

	}

};
