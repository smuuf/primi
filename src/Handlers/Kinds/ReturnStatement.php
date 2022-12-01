<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Structures\CallRetval;

class ReturnStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$retval = new CallRetval(
			\array_key_exists('subject', $node)
				? HandlerFactory::runNode($node['subject'], $context)
				: \null
		);

		$context->setRetval($retval);

	}

}
