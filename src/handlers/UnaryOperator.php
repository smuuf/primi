<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\ISupportsUnary;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class UnaryOperator extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		$variableName = $node['core']['text'];
		$value = $context->getVariable($variableName);
		$unaryNode = $node['pre'] ?? $node['post'] ?? false;

		if ($unaryNode && !$value instanceof ISupportsUnary) {
			throw new ErrorException(sprintf(
				"Cannot perform unary operation on '%s'",
				$value::TYPE
			), $node);
		}

		if ($unaryNode) {
			$operator = $unaryNode['text'];
			$newValue = $value->doUnary($operator);
			$context->setVariable($variableName, $newValue);
			return isset($node['pre']) ? $newValue : $value;
		}

		return false;

	}

}
