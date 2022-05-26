<?php

declare(strict_types=1);

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Structures\FnContainer;

class AnonymousFunction extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$module = $context->getCurrentModule();
		$name = \sprintf("%s.<anonymous>()", $module->getName());

		$fn = FnContainer::build(
			$node['body'],
			$name,
			$module,
			$node['params'],
			$context->getCurrentScope(),
		);

		return new FuncValue($fn);

	}

	public static function reduce(array &$node): void {

		if (isset($node['params'])) {
			$node['params'] =
				FunctionDefinition::prepareParameters($node['params']);
		} else {
			$node['params'] = [];
		}

	}

}
