<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class VariableCore extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {
		return $context->getVariable($node['text']);
	}

}