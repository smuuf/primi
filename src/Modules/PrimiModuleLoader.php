<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\Primi\Source;
use \Smuuf\Primi\CallFrame;
use \Smuuf\Primi\DirectInterpreter;
use \Smuuf\Primi\Values\ModuleValue;
use \Smuuf\Primi\Helpers\Wrappers\ContextPushPopWrapper;

class PrimiModuleLoader extends AbstractModuleLoader {

	protected static function buildModulePath(
		string $base,
		array $pathParts
	): string {

		$path = \implode('/', $pathParts);
		return "$base/{$path}.primi";

	}

	public function loadModule(string $path, string $name): ModuleValue {

		$source = new Source($path, \true, $name);

		// Imported module will have its own scope.
		$scope = $this->ctx->buildNewGlobalScope();
		$frame = new CallFrame("<module: {$name}>");

		$wrapper = new ContextPushPopWrapper($this->ctx, $frame, $scope);
		$wrapper->wrap(function($ctx) use ($source) {

			// Execute the source code within the new scope.
			DirectInterpreter::execute($source, $ctx);

		});

		return new ModuleValue($name, $scope);

	}

}
