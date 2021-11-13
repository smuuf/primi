<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Structures\FnContainer;

class AnonymousFunction extends SimpleHandler {

	protected static function handle(array $node, Context $context) {

		$fn = FnContainer::build(
			$node['body'],
			"<anonymous>()",
			$context->getCurrentModule(),
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
