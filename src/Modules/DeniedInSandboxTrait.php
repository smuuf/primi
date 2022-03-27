<?php

namespace Smuuf\Primi\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;

/**
 * Native Primi modules (written in PHP) implemented by a PHP class which
 * uses this trait will be forbidden to be imported if Primi is running
 * in a sandbox mode.
 */
trait DeniedInSandboxTrait {

	public function execute(Context $ctx): array {

		if ($ctx->getConfig()->getSandboxMode()) {
			throw new RuntimeError("Access to module disabled when in sandbox");
		}

		return parent::execute($ctx);

	}

}
