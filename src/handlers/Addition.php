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
class Addition extends \Smuuf\Primi\Object implements IHandler {

	public static function handle(array $node, Context $context) {

		// Handle situation with solo operands (which wouldn't be represented as array).
		// Do it by placing solo operands into arrays.
		if (isset($node['operands']['name'])) {
			$node['operands'] = [$node['operands']];
		}

		// Do the same with operators.
		if (isset($node['ops']['name'])) {
			$node['ops'] = [$node['ops']];
		}

		// Handle the first operand.
		// (array_shift reindexes the array, so we need do this a bit low-lewel.)
		$first = reset($node['operands']);
		unset($node['operands'][0]);
		$handler = HandlerFactory::get($first['name']);
		$result = $handler::handle($first, $context);

		// Go through each of the operands and build the final result value combining the operand's value with the
		// so-far-result. The operator determining the operands's effect on the result always has the "n-1" index.
		foreach ($node['operands'] as $index => $operandNode) {

			$handler = HandlerFactory::get($operandNode['name']);
			$tmp = $handler::handle($operandNode, $context);

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

}
