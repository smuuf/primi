<?php

namespace Smuuf\Primi\Handlers\Types;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Func;
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

		// Prepare list of parameters.
		$params = [];
		if (isset($node['params'])) {
			// Make sure this is always list, even with one item.
			$node['params'] = Func::ensure_indexed($node['params']);
			foreach ($node['params'] as $p) {
				$params[] = $p['text'];
			}
		}

		$node['params'] = $params;

	}

}
