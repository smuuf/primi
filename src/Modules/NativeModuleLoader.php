<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\Primi\CallFrame;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Helpers\Wrappers\ContextPushPopWrapper;

class NativeModuleLoader extends AbstractModuleLoader {

	protected static function buildModulePath(
		string $base,
		array $pathParts
	): string {

		$path = \implode('/', $pathParts);
		return "$base/{$path}.php";

	}

	public function loadModule(string $path, string $name): ModuleValue {

		// Imported module will have its own scope.
		$scope = $this->ctx->buildNewGlobalScope();
		$frame = new CallFrame("<module: {$name}>");

		$wrapper = new ContextPushPopWrapper($this->ctx, $frame, $scope);
		$wrapper->wrap(function() use ($path, $scope) {

			$loader = fn($modulePath) => require $modulePath;
			$scope->setVariables($loader($path)->execute($this->ctx));

		});

		return new ModuleValue($name, $scope);

	}

}
