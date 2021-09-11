<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Context;

class NativeModuleLoader {

	use StrictObject;

	public static function loadModule(Context $ctx, string $filepath): void {

		// Closure to block access of the file's code to this PHP scope.
		$loader = fn($modulePath) => require $modulePath;

		$dict = NativeModuleExecutor::execute(
			$ctx,
			$loader($filepath)
		);

		$ctx->getCurrentScope()->setVariables($dict);

	}

}
