<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\ISupportsMultiplication;
use \Smuuf\Primi\ISupportsDivision;
use \Smuuf\Primi\ErrorException;
use \Smuuf\Primi\HandlerFactory;
use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers;

/**
 * Node fields:
 * operands: List of operand nodes.
 * ops: List of nodes acting as operators between the operands.
 */
class Multiplication extends \Smuuf\Primi\StrictObject implements IHandler, IReducer {

	public static function handle(array $node, Context $context) {

		Helpers::ensureIndexed($node['ops']);

		$operands = $node['operands'];

		$firstOperand = array_shift($operands);
		$handler = HandlerFactory::get($firstOperand['name']);
		$result = $handler::handle($firstOperand, $context);

		// Go through each of the operands and continuously calculate the result
		// value combining the operand's value with the result-so-far. The
		// operator determining the operands's effect on the result has always
		// the "n" index. (It would be "n-1" but we shifted the first operand
		// already.)
		foreach ($operands as $index => $operandNode) {

			$handler = HandlerFactory::get($operandNode['name']);
			$tmp = $handler::handle($operandNode, $context);

			// Extract the text of the assigned operator node.
			$op = $node['ops'][$index]['text'];

			try {

				if ($op === "*" && $result instanceof ISupportsMultiplication) {
					$result = $result->doMultiplication($tmp);
				} elseif ($op === "/" && $result instanceof ISupportsDivision) {
					$result = $result->doDivision($tmp);
				} else {
					throw new \TypeError;
				}

			} catch (\TypeError $e) {

				throw new ErrorException(sprintf(
					"Cannot %s types '%s' and '%s'",
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
