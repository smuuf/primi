<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Handlers\SimpleHandler;

/**
 * Node fields:
 * left: A comparison expression node.
 * right: Node representing contents of code to execute if left-hand result is truthy.
 */
class IfStatement extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		// Execute the left-hand node and get its return value.
		$result = HandlerFactory::runNode($node['cond'], $context);

		// If the result of the left hand equals to truthy value,
		// execute the code branch stored in the right-hand node.
		if ($result->isTruthy()) {
			HandlerFactory::runNode($node['block'], $context);
			return;
		}

		// If there are any elifs, go through each one of them and if condition
		// of any them evaluates as truthy, run their block (but only the first
		// elif with the truthy condition).
		foreach ($node['elifs'] as $elif) {
			$result = HandlerFactory::runNode($elif['cond'], $context);
			if ($result->isTruthy()) {
				HandlerFactory::runNode($elif['block'], $context);
				return;
			}
		}

		// Check existence of "else" block and execute it, if it's present.
		if (isset($node['elseBlock'])) {
			HandlerFactory::runNode($node['elseBlock'], $context);
		}

	}

	public static function reduce(array &$node): void {

		$elifs = [];
		foreach ($node['elifCond'] ?? [] as $i => $elifCond) {
			$elifs[] = [
				'cond' => $elifCond,
				'block' => $node['elifBlock'][$i],
			];
		}

		unset($node['elifCond']);
		unset($node['elifBlock']);
		$node['elifs'] = $elifs;

	}

}
