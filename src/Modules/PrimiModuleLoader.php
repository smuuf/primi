<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use Smuuf\StrictObject;
use Smuuf\Primi\Context;
use Smuuf\Primi\Ex\SyntaxError;
use Smuuf\Primi\Code\SourceFile;
use Smuuf\Primi\Values\ModuleValue;

class PrimiModuleLoader {

	use StrictObject;

	public static function loadModule(
		Context $ctx,
		string $filepath,
		ModuleValue $module,
	): void {

		$source = new SourceFile($filepath);

		$bytecode = SyntaxError::catch(
			$ctx,
			fn() => $ctx->getBytecodeProvider()->getBytecode($source),
		);

		$frame = $ctx->buildFrame(
			name: '<module>',
			bytecode: $bytecode,
			scope: $module->getCoreValue(),
			module: $module,
		);

		$ctx->runFrame($frame);

	}

}
