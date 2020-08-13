<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\FnContainer;

use function \Smuuf\Primi\Helpers\ensure_indexed as primifn_ensure_indexed;

class FunctionDefinition extends SimpleHandler {

	const NODE_NEEDS_TEXT = \true;

	public static function handle(array $node, Context $context) {

		$fnc = FnContainer::build($node['body'], $node['params'], $context);
		$context->setVariable($node['fnName'], new FuncValue($fnc));

	}

	public static function reduce(array &$node): void {

		// Prepare function name.
		$node['fnName'] = $node['function']['text'];
		unset($node['function']);

		// Prepare list of parameters.
		$params = [];
		if (isset($node['params'])) {
			// Make sure this is always list, even with one item.
			$node['params'] = primifn_ensure_indexed($node['params']);
			foreach ($node['params'] as $a) {
				$params[] = $a['text'];
			}
		}
		$node['params'] = $params;

	}

}
