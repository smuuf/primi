<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Wrappers;

use Smuuf\StrictObject;

/**
 * Some PHP internals emit E_WARNINGs even though we want to handle them
 * ourselves. Such code can be wrapped with this - to temporarily suppress
 * PHP warnings.
 *
 * Any custom error handler set by set_error_handler() will (sad)y) still be
 * executed, so that error handler would need to actually check error reporting
 * level itself to _really_ skip warnings.
 *
 * > It is important to remember that the standard PHP error handler is
 * > completely bypassed for the error types specified by error_levels unless
 * > the callback function returns false. error_reporting() settings will have
 * > no effect and your error handler will be called regardless - however you
 * > are still able to read the current value of error_reporting and act
 * > appropriately.
 *
 * ... from PHP docs.
 *
 * @see https://www.php.net/manual/en/function.set-error-handler.php
 */
class IgnoreWarningsWrapper extends AbstractWrapper {

	use StrictObject;

	private int $originalErrorReporting;

	public function executeBefore(): void {
		$this->originalErrorReporting = \error_reporting();
		\error_reporting($this->originalErrorReporting ^ \E_WARNING);
	}

	public function executeAfter(): void {
		\error_reporting($this->originalErrorReporting);
	}

}
