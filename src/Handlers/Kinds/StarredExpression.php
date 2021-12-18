<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Ex\RuntimeError;
use \Smuuf\Primi\Ex\EngineInternalError;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Handlers\HandlerFactory;
use \Smuuf\Primi\Structures\CallArgs;

class StarredExpression extends SimpleHandler {

	const NODE_NEEDS_TEXT = \false;

	public const STARS_NONE = 0;
	public const STARS_ONE = 1;
	public const STARS_TWO = 2;

	protected static function handle(array $node, Context $context) {

		/** @var AbstractValue */
		$value = HandlerFactory::runNode($node['expr'], $context);

		$values = [];
		switch (\true) {
			case $node['stars'] === self::STARS_ONE:

				$iter = $value->getIterator();
				if ($iter === \null) {
					throw new RuntimeError("Cannot unpack non-iterable");
				}

				foreach ($iter as $v) {
					$values[] = $v;
				}

				return new CallArgs($values);

			case $node['stars'] === self::STARS_TWO:
				return new CallArgs(
					[],
					Func::couples_to_variables_array(
						Func::mapping_to_couples($value),
						'Variable or argument name'
					)
				);

		}

		throw new EngineInternalError("Starred expression with wrong type");

	}

}
