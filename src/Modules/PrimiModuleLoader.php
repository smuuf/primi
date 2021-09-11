<?php

declare(strict_types=1);

namespace Smuuf\Primi\Modules;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\DirectInterpreter;
use \Smuuf\Primi\Code\SourceFile;

class PrimiModuleLoader {

	use StrictObject;

	public static function loadModule(Context $ctx, string $filepath): void {

		$source = new SourceFile($filepath);
		$ast = $ctx->getAstProvider()->getAst($source);

		// Execute the source code within the new scope.
		DirectInterpreter::execute($ast, $ctx);

	}

}
