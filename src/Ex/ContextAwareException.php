<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Context;

/**
 * Helper exception that is thrown only in `SimpleHandler` and `ChainedHandler`
 * and converted into `RuntimeError` when caught in `DirectInterpreter::execute()`.
 */
class ContextAwareException extends EngineException {

	public function __construct(
		string $originalMsg,
		array $node = null,
		Context $ctx = null
	) {

		// If there's any traceback, add it to the error.
		if ($tb = $ctx->getTraceback()) {

			$tbMsg = "\nTraceback:";
			foreach ($tb as $level => $callId)  {
				$tbMsg .= "\n[$level] $callId";
			}

		} else {
			$tbMsg = '';
		}

		$msg = \sprintf(
			"%s @ line %s, position %s%s\n",
			$originalMsg,
			$node['_l'],
			$node['_p'],
			$tbMsg
		);

		parent::__construct($msg);

	}

}
