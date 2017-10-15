<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsDivision;
use \Smuuf\Primi\UnsupportedOperationException;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;

/**
 * Node fields:
 * operands: List of operand nodes.
 * ops: List of nodes acting as operators between the operands.
 */
class Multiplication extends \Smuuf\Primi\Object implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		// Make sure the potentially multiple nodes are represented as being indexed.
		if (!isset($node['ops'][0])) {
			$node['ops'] = [$node['ops']];
		}

		// Go through each of the operands and build the final result value combining the operand's value with the
		// so-far-result. The operator determining the operands's effect on the result always has the "n-1" index.
		$first = \true;
		$result = \null;
		foreach ($node['operands'] as $index => $operandNode) {

			$handler = HandlerFactory::get($operandNode['name']);
			if ($first) {
				$result = $handler::handle($operandNode, $context);
				$first = \false;
				continue;
			} else {
				$tmp = $handler::handle($operandNode, $context);
			}

			// Extract the text of the assigned operator node.
			$op = $node['ops'][$index - 1]['text'];

			try {

				if ($op === "*" && $result instanceof ISupportsMultiplication) {
					$result = $result->doMultiplication($tmp);
				} elseif ($op === "/" && $result instanceof ISupportsDivision) {
					$result = $result->doDivision($tmp);
				} else {
					throw new UnsupportedOperationException;
				}

			} catch (UnsupportedOperationException $e) {

				throw new ErrorException(sprintf(
					"Cannot %s: '%s' and '%s'",
					$op ===  "*" ? "multiply" : "divide",
					$result::TYPE,
					$tmp::TYPE
				), $node);

			}

		}

		return $result;

	}

	public static function reduce(array $node) {

		// No need to represent this kind of node as Multiplication when there's only one operand.
		// Take the only operand here and subtitute the Multiplication node with it.
		if (isset($node['operands']['name'])) {
			return $node['operands'];
		}

	}

}
