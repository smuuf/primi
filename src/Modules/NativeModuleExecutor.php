<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\MethodExtractor;

class NativeModuleExecutor {

	use StrictObject;

	/**
	 * @return array<string, AbstractValue|mixed> Dict array that represents the
	 * contents of the module.
	 */
	public static function execute(Context $ctx, NativeModule $module): array {

		// Basic execution.
		$attrs = $module->execute($ctx);

		// Extract additional functions from object.
		$functions = MethodExtractor::extractMethods($module);

		return \array_merge($attrs, $functions);

	}

}
