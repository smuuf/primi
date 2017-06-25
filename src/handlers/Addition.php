<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * operands: List of operand nodes.
 * ops: List of nodes acting as operators between the operands.
 */
class Addition extends \Smuuf\Primi\Object implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		// Do the same with operators.
		if (isset($node['ops']['name'])) {
			$node['ops'] = [$node['ops']];
		}

		// Go through each of the operands and build the final result value combining the operand's value with the
		// so-far-result. The operator determining the operands's effect on the result always has the "n-1" index.
		$first = true;
		foreach ($node['operands'] as $index => $operandNode) {

			$handler = HandlerFactory::get($operandNode['name']);

			if ($first) {
				$result = $handler::handle($operandNode, $context);
				$first = false;
				continue;
			} else {
				$tmp = $handler::handle($operandNode, $context);
			}

			// Extract the text of the assigned operator node.
			$op = $node['ops'][$index - 1]['text'];

			if ($op === '+') {

				if (is_numeric($result) && is_numeric($tmp)) {
					$result += $tmp;
				} else {
					$result .= $tmp;
				}

			} else {

				if (!is_numeric($result) || !is_numeric($tmp)) {
					throw new ErrorException(sprintf(
						"Trying to subtract non-numeric values: '%s' and '%s'",
						$result,
						$tmp
					));
				}

				$result -= $tmp;
			}

		}

		return $result;

	}

	public static function reduce(array $node) {

		// No need to represent this kind of node as Addition when there's only one operand.
		// Take the only operand here and subtitute the Addition node with it.
		if (isset($node['operands']['name'])) {
			return $node['operands'];
		}

	}

}
