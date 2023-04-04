<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use Smuuf\StrictObject;
use Smuuf\Primi\Context;
use Smuuf\Primi\Ex\EngineError;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Values\ModuleValue;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Extensions\MethodExtractor;

class NativeModuleLoader {

	use StrictObject;

	public static function loadModule(
		Context $ctx,
		string $filepath,
		ModuleValue $module,
	): void {

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
			Exceptions::piggyback(
				StaticExceptionTypes::getRuntimeErrorType(),
				"Access to native module forbidden when in sandbox",
			);
		}

		$dict = self::execute($ctx, $result);

		$scope = $module->getCoreValue();
		$scope->setVariables($dict);

	}

	/**
	 * @return array<string, AbstractValue|mixed> Dict array that represents the
	 * contents of the module.
	 */
	private static function execute(Context $ctx, NativeModule $module): array {

		// Basic execution.
		$attrs = $module->execute($ctx);

		// Extract additional functions from object.
		$functions = MethodExtractor::extractMethods($module);

		return \array_merge($attrs, $functions);

	}


}
