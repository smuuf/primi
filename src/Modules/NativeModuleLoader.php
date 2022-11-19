<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\Primi\Ex\RuntimeError;

class NativeModuleLoader {

	use StrictObject;

	public static function loadModule(Context $ctx, string $filepath): void {

		// Closure to block access of the file's code to this PHP scope.
		$loader = static fn($modulePath) => require $modulePath;
		$result = $loader($filepath);

		if (!$result instanceof NativeModule) {
			throw new EngineError("Native module loader encountered a non-native module file");
		}

		$traits = \class_uses($result, \false) ?: [];
		if (
			!\in_array(AllowedInSandboxTrait::class, $traits, \true)
			&& $ctx->getConfig()->getSandboxMode()
		) {
			throw new RuntimeError("Access to native module forbidden when in sandbox");
		}

		$dict = NativeModuleExecutor::execute($ctx, $result);
		$ctx->getCurrentScope()->setVariables($dict);

	}

}
