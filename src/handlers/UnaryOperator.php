<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Structures\Value;

use \Smuuf\Primi\ISupportsUnary;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\InternalException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class UnaryOperator extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		$unaryNode = $node['pre'] ?? $node['post'] ?? false;

		// Short circuit this method if there is no unary stuff to do.
		if ($unaryNode === false) {
			throw new InternalException("Handling unary operator without unary node.");
		}

		$handler = HandlerFactory::get($node['core']['name']);
		$value = $handler::handle($node['core'], $context)->getValue();

		if (!$value instanceof ISupportsUnary) {
			throw new ErrorException(sprintf(
				"Cannot perform unary operation on '%s'",
				$value::TYPE
			), $node);
		}

		$operator = $unaryNode['text'];
		$newValue = $value->doUnary($operator);
		$context->setVariable($variableName, $newValue);
		return isset($node['pre']) ? $newValue : $value;

	}

}
