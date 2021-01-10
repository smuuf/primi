<?php

declare(strict_types=1);

namespace Smuuf\Primi\Ex;

use \Smuuf\Primi\Context;

/**
 * Helper exception that is thrown only in `SimpleHandler` and `ChainedHandler`.
 */
class ContextAwareException extends EngineException {

	public function __construct(
		string $originalMsg,
		array $node = null,
		Context $ctx = null
	) {



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
