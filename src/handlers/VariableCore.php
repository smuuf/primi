<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\UndefinedVariableException;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class VariableCore extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {
		try {
			return $context->getVariable($node['text']);
		} catch (UndefinedVariableException $e) {
			throw new ErrorException("Undefined variable '$node[text]'");
		}
	}

}