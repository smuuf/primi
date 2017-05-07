<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

class UnaryOperator extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $parentNode, Context $context) {

		$variableName = $parentNode['core']['text'];
		$value = $context->getVariable($variableName);

		if (isset($parentNode['pre'])) {

			if ($parentNode['pre']['text'] === "++") {
				$context->setVariable($variableName, $value + 1);
				return true;
			}

			if ($parentNode['pre']['text'] === "--") {
				$context->setVariable($variableName, $value - 1);
				return true;
			}

		} elseif (isset($parentNode['post'])) {

			if ($parentNode['post']['text'] === "++") {
				$context->setVariable($variableName, $value + 1);
				return true;
			}

			if ($parentNode['post']['text'] === "--") {
				$context->setVariable($variableName, $value - 1);
				return true;
			}

		}

	}

	public static function getReturnValue(array $parentNode, $originalValue) {

		if (isset($parentNode['pre'])) {

			if ($parentNode['pre']['text'] === "++") {
				return $originalValue + 1;
			}

			if ($parentNode['pre']['text'] === "--") {
				return $originalValue - 1;
			}

		}

		// Post-operators always return the original value.
		return $originalValue;

	}

}