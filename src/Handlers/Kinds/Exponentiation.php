<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\BinaryOperationError;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;

class Exponentiation extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$operand = HandlerFactory::runNode($node['operand'], $context);
		$factor = HandlerFactory::runNode($node['factor'], $context);

		$result = $operand->doPower($factor);
		if ($result === \null) {
			throw new BinaryOperationError('**', $operand, $factor);
		}

		return $result;

	}

	public static function reduce(array &$node): void {

		// If there is no factor, then there's no need to keep this as
		// a complex node of this type. Reduce this node to its only operand.
		if (!isset($node['factor'])) {
			$node = $node['operand'];
		}

	}

}
