<?php

namespace Smuuf\Primi\Handlers\Kinds;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Handlers\SimpleHandler;
use \Smuuf\Primi\Values\FuncValue;
use \Smuuf\Primi\Structures\FnContainer;

class FunctionDefinition extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	protected static function handle(array $node, Context $context) {

		$name = "{$node['fnName']}()";
		$currentScope = $context->getCurrentScope();

		$fnc = FnContainer::build(
			$node['body'],
			$name,
			$context->getCurrentModule(),
			$node['params'],
			$currentScope,
		);

		$currentScope->setVariable($node['fnName'], new FuncValue($fnc));

	}

	public static function reduce(array &$node): void {

		// Prepare function name.
		$node['fnName'] = $node['function']['text'];
		unset($node['function']);

		// Prepare list of parameters.
		$params = [];
		if (isset($node['params'])) {
			// Make sure this is always list, even with one item.
			$node['params'] = Func::ensure_indexed($node['params']);
			foreach ($node['params'] as $a) {
				$params[] = $a['text'];
			}
		}
		$node['params'] = $params;

	}

}
